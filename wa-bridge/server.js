const express = require('express');
const qrcode = require('qrcode');
const { Client } = require('whatsapp-web.js');
const multer = require('multer');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3333;

// Configure multer for memory storage (no disk writes)
const upload = multer({
    storage: multer.memoryStorage(),
    limits: { fileSize: 25 * 1024 * 1024 } // 25MB limit
});

// Middleware - for text/form data
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ limit: '10mb', extended: true }));

// CORS middleware
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    if (req.method === 'OPTIONS') {
        return res.sendStatus(200);
    }
    next();
});

// State management
let qrCode = null;
let isReady = false;
let client = null;

// Initialize WhatsApp Client
function initializeClient() {
    const sessionsDir = path.join(__dirname, 'sessions');
    if (!fs.existsSync(sessionsDir)) {
        fs.mkdirSync(sessionsDir);
    }

    client = new Client({
        session: null, // Fresh session each time
        puppeteer: {
            args: ['--no-sandbox', '--disable-setuid-sandbox'],
        },
    });

    client.on('qr', async (qr) => {
        console.log('QR Code received');
        try {
            qrCode = await qrcode.toDataURL(qr);
        } catch (err) {
            console.error('Error generating QR code:', err);
        }
    });

    client.on('ready', () => {
        console.log('WhatsApp client is ready!');
        isReady = true;
        qrCode = null;
    });

    client.on('auth_failure', () => {
        console.log('Authentication failed');
        isReady = false;
        qrCode = null;
    });

    client.on('disconnected', (reason) => {
        console.log('WhatsApp disconnected:', reason);
        isReady = false;
        qrCode = null;
        // Reinitialize client
        setTimeout(() => {
            initializeClient();
        }, 5000);
    });

    client.initialize();
}

// Initialize client on startup
initializeClient();

// Routes

/**
 * GET /qr
 * Returns QR code and ready status
 */
app.get('/qr', (req, res) => {
    try {
        if (isReady) {
            return res.json({
                ready: true,
                qr: null,
            });
        }

        if (qrCode) {
            // Remove data:image/png;base64, prefix
            const qrBase64 = qrCode.replace(/^data:image\/png;base64,/, '');
            return res.json({
                ready: false,
                qr: qrBase64,
            });
        }

        return res.json({
            ready: false,
            qr: null,
            message: 'Waiting for QR code...',
        });
    } catch (err) {
        console.error('Error in /qr route:', err);
        return res.status(500).json({ error: err.message });
    }
});

/**
 * POST /send
 * Send a message to a phone number
 * Body: { phone: "string", message: "string" }
 */
app.post('/send', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({ error: 'Phone and message are required' });
        }

        if (!isReady) {
            return res.status(409).json({ error: 'WhatsApp not ready. Scan QR code first.' });
        }

        // Format phone number (remove spaces, add @ for WhatsApp)
        const formattedPhone = phone.replace(/\D/g, '');
        const chatId = formattedPhone + '@c.us';

        // Send message
        const result = await client.sendMessage(chatId, message);

        res.json({
            success: true,
            message: 'Message sent',
            messageId: result.id.id,
        });
    } catch (err) {
        console.error('Error in /send route:', err);
        return res.status(500).json({ error: err.message });
    }
});

/**
 * POST /send-media
 * Send a file/media to a phone number
 * Body: { phone: "string", filename: "string", mime: "string", base64: "string", caption: "string" }
 */
app.post('/send-media', async (req, res) => {
    try {
        const { phone, filename, mime, base64, caption } = req.body;

        if (!phone || !filename || !mime || !base64) {
            return res.status(400).json({ error: 'Phone, filename, mime, and base64 data are required' });
        }

        if (!isReady) {
            return res.status(409).json({ error: 'WhatsApp not ready. Scan QR code first.' });
        }

        // Import MessageMedia
        const { MessageMedia } = require('whatsapp-web.js');

        // Format phone number (remove spaces, add @ for WhatsApp)
        const formattedPhone = phone.replace(/\D/g, '');
        const chatId = formattedPhone + '@c.us';

        // Create media object with base64 (NO data: prefix)
        const media = new MessageMedia(mime, base64, filename);

        // Send message with media and optional caption
        const result = await client.sendMessage(chatId, media, { caption: caption || '' });

        res.json({
            success: true,
            message: 'Media sent successfully',
            messageId: result.id.id,
        });
    } catch (err) {
        console.error('Error in /send-media route:', err);
        return res.status(500).json({ error: err.message });
    }
});

/**
 * POST /send-media-upload
 * Send a file/media via multipart file upload
 * Fields:
 *   - phone (string): Phone number
 *   - caption (string, optional): Message caption
 *   - file (binary): PDF or media file
 */
app.post('/send-media-upload', upload.single('file'), async (req, res) => {
    try {
        const { phone, caption } = req.body;

        if (!phone || !req.file) {
            return res.status(400).json({ error: 'Phone and file are required' });
        }

        if (!isReady) {
            return res.status(409).json({ error: 'WhatsApp not ready. Scan QR code first.' });
        }

        // Import MessageMedia
        const { MessageMedia } = require('whatsapp-web.js');

        // Format phone number (digits only, add @ for WhatsApp)
        const formattedPhone = phone.replace(/\D/g, '');
        const chatId = formattedPhone + '@c.us';

        // Convert file buffer to base64
        const base64 = req.file.buffer.toString('base64');

        // Determine MIME type
        const mimetype = req.file.mimetype || 'application/pdf';
        const filename = req.file.originalname || 'document.pdf';

        // Create media object
        const media = new MessageMedia(mimetype, base64, filename);

        // Send message with media and optional caption
        const result = await client.sendMessage(chatId, media, { caption: caption || '' });

        console.log(`WhatsApp message sent to ${formattedPhone}: ${result.id.id}`);

        res.json({
            success: true,
            message: 'Media sent successfully',
            messageId: result.id.id,
        });
    } catch (err) {
        console.error('Error in /send-media-upload route:', err);
        return res.status(500).json({ error: err.message });
    }
});

/**
 * GET /status
 * Get current connection status
 */
app.get('/status', (req, res) => {
    res.json({
        ready: isReady,
        hasQr: qrCode !== null,
    });
});

/**
 * POST /logout
 * Logout from WhatsApp
 */
app.post('/logout', async (req, res) => {
    try {
        if (client) {
            await client.logout();
            isReady = false;
            qrCode = null;
            return res.json({ success: true, message: 'Logged out' });
        }
        return res.status(400).json({ error: 'Client not initialized' });
    } catch (err) {
        console.error('Error logging out:', err);
        return res.status(500).json({ error: err.message });
    }
});

// Health check
app.get('/health', (req, res) => {
    res.json({ status: 'ok', timestamp: new Date() });
});

// Start server
app.listen(PORT, () => {
    console.log(`WhatsApp Bridge server running on http://127.0.0.1:${PORT}`);
    console.log('Waiting for WhatsApp client initialization...');
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\nShutting down gracefully...');
    if (client) {
        await client.destroy();
    }
    process.exit(0);
});
