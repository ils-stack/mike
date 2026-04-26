<table id="accountsTable" class="table table-sm table-hover align-middle">

    <thead class="table-light">
        <tr>
            <th>Name</th>
            <th>Contract Number</th>
            <th>Account Number</th>
            <th class="text-end">Market Value</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

        @forelse($contracts as $contract)

        <tr>
            <td>{{ $contract->account_description }}</td>

            <td>{{ $contract->contract_number }}</td>

            <td>{{ $contract->account_number }}</td>

            <td class="text-end">
                R {{ number_format($contract->market_value,2) }}
            </td>

            <td class="text-center">
                <span class="text-muted" title="Holdings temporarily disabled" style="cursor: not-allowed; opacity: 0.6;">
                    <i class="fas fa-eye"></i>
                </span>
            </td>
        </tr>

        @empty

        <tr>
            <td></td>
            <td></td>
            <td class="text-center text-muted">No contracts found</td>
            <td></td>
            <td></td>
        </tr>

        @endforelse

    </tbody>

</table>
