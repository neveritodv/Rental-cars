
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Montant</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data ?? [] as $item)
        <tr>
            <td>{{ $item['date'] ?? '' }}</td>
            <td>{{ $item['type'] ?? '' }}</td>
            <td>{{ $item['description'] ?? '' }}</td>
            <td>{{ isset($item['amount']) ? $item['amount'] : '' }}</td>
            <td>{{ $item['status'] ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>