<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
        <tr><th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Status</th><th class="p-3 text-right">Total</th><th class="p-3">Created</th></tr>
    </thead>
    <tbody class="divide-y">
        @forelse ($orders as $order)
            <tr class="hover:bg-slate-50">
                <td class="p-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-brand-600 hover:underline">{{ $order->number }}</a></td>
                <td class="p-3">{{ $order->customer_name }}<div class="text-xs text-slate-500">{{ $order->customer_email }}</div></td>
                <td class="p-3"><x-status-badge :status="$order->status" /></td>
                <td class="p-3 text-right">{{ \App\Support\Money::format($order->total) }}</td>
                <td class="p-3 whitespace-nowrap text-slate-500">{{ $order->created_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-4 text-slate-500">No orders.</td></tr>
        @endforelse
    </tbody>
</table>
