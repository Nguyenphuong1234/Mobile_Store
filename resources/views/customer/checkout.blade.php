@extends('layouts.store')

@section('content')
    @php
        $paymentMethods = \App\Models\Order::paymentMethods();
        $selectedAddressId = old('address_id', $addresses->firstWhere('is_default', true)?->id);
        $selectedPaymentMethod = old('payment_method', \App\Models\Order::PAYMENT_COD);
        $checkoutStep = $errors->has('payment_method') ? 2 : 1;
    @endphp

    <h1 class="mb-6 text-2xl font-semibold">Xác nhận đặt hàng</h1>

    <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
        <form id="checkout-form" class="rounded-lg border border-zinc-200 bg-white p-5" method="POST" action="{{ route('checkout.store') }}" data-initial-step="{{ $checkoutStep }}">
            @csrf
            <div class="mb-5 grid grid-cols-2 overflow-hidden rounded-md border border-zinc-200 text-sm font-medium">
                <div data-step-indicator="1" class="px-3 py-2 text-center">1. Giao hàng</div>
                <div data-step-indicator="2" class="border-l border-zinc-200 px-3 py-2 text-center">2. Thanh toán</div>
            </div>

            <section data-checkout-step="1" @class(['hidden' => $checkoutStep !== 1])>
                <h2 class="text-lg font-semibold">Thông tin giao hàng</h2>

                @if ($addresses->isNotEmpty())
                    <div class="mt-4 space-y-3">
                        @foreach ($addresses as $address)
                            <label class="flex cursor-pointer gap-3 rounded-md border border-zinc-200 p-3">
                                <input type="radio" name="address_id" value="{{ $address->id }}" @checked((string) $selectedAddressId === (string) $address->id)>
                                <span>
                                    <span class="block font-medium">{{ $address->recipient_name }} · {{ $address->recipient_phone }}</span>
                                    <span class="block text-sm text-zinc-600">{{ $address->fullAddress() }}</span>
                                </span>
                            </label>
                        @endforeach

                        <label class="flex cursor-pointer gap-3 rounded-md border border-zinc-200 p-3">
                            <input type="radio" name="address_id" value="" @checked(blank($selectedAddressId))>
                            <span class="font-medium">Nhập địa chỉ mới</span>
                        </label>
                    </div>
                @endif

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="Tên người nhận" data-manual-address-field>
                    <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="Số điện thoại" data-manual-address-field>
                    <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="address_line" value="{{ old('address_line') }}" placeholder="Số nhà, tên đường" data-manual-address-field>
                    <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="ward" value="{{ old('ward') }}" placeholder="Phường/xã">
                    <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="district" value="{{ old('district') }}" placeholder="Quận/huyện">
                    <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="city" value="{{ old('city') }}" placeholder="Tỉnh/thành phố" data-manual-address-field>
                    <textarea class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="customer_note" rows="3" placeholder="Ghi chú giao hàng">{{ old('customer_note') }}</textarea>
                </div>

                <button class="mt-6 rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="button" data-next-step>Tiếp tục</button>
            </section>

            <section data-checkout-step="2" @class(['hidden' => $checkoutStep !== 2])>
                <h2 class="text-lg font-semibold">Phương thức thanh toán</h2>

                <div class="mt-4 space-y-3">
                    <label class="flex cursor-pointer gap-3 rounded-md border border-zinc-200 p-4">
                        <input type="radio" name="payment_method" value="{{ \App\Models\Order::PAYMENT_COD }}" required @checked($selectedPaymentMethod === \App\Models\Order::PAYMENT_COD)>
                        <span>
                            <span class="block font-medium">{{ $paymentMethods[\App\Models\Order::PAYMENT_COD] }}</span>
                            <span class="block text-sm text-zinc-600">Thanh toán trực tiếp khi đơn hàng được giao.</span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer gap-3 rounded-md border border-zinc-200 p-4">
                        <input type="radio" name="payment_method" value="{{ \App\Models\Order::PAYMENT_BANK_TRANSFER }}" required @checked($selectedPaymentMethod === \App\Models\Order::PAYMENT_BANK_TRANSFER)>
                        <span>
                            <span class="block font-medium">{{ $paymentMethods[\App\Models\Order::PAYMENT_BANK_TRANSFER] }}</span>
                            <span class="block text-sm text-zinc-600">Quét VietQR BIDV 3711366505.</span>
                        </span>
                    </label>
                </div>

                <div id="vietqr-panel" @class(['mt-4 rounded-md border border-zinc-200 bg-zinc-50 p-4', 'hidden' => $selectedPaymentMethod !== \App\Models\Order::PAYMENT_BANK_TRANSFER])>
                    <a href="https://img.vietqr.io/image/BIDV-3711366505-qr_only.png" target="_blank" rel="noopener">
                        <img class="mx-auto size-56 rounded-md border border-zinc-200 bg-white object-contain p-3" src="https://img.vietqr.io/image/BIDV-3711366505-qr_only.png" alt="VietQR BIDV 3711366505">
                    </a>
                    <a class="mt-3 block text-center text-sm font-medium text-zinc-700 hover:text-zinc-950" href="https://img.vietqr.io/image/BIDV-3711366505-qr_only.png" target="_blank" rel="noopener">Mở VietQR</a>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50" type="button" data-prev-step>Quay lại</button>
                    <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Hoàn thành</button>
                </div>
            </section>
        </form>

        <aside class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="text-lg font-semibold">Sản phẩm trong đơn</h2>
            <div class="mt-4 space-y-3">
                @foreach ($cart->items as $item)
                    <div class="flex justify-between gap-4 text-sm">
                        <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span class="font-medium">{{ number_format($item->lineTotal(), 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 flex justify-between border-t border-zinc-200 pt-4 text-lg font-semibold">
                <span>Tổng tiền</span>
                <span>{{ number_format($cart->subtotal(), 0, ',', '.') }} đ</span>
            </div>
            <div class="mt-2 text-sm text-zinc-500">Phương thức thanh toán: <span id="checkout-payment-summary">{{ $paymentMethods[$selectedPaymentMethod] ?? 'Chưa chọn' }}</span></div>
        </aside>
    </div>

    <script>
        (() => {
            const form = document.getElementById('checkout-form');
            const qrPanel = document.getElementById('vietqr-panel');
            const paymentSummary = document.getElementById('checkout-payment-summary');
            const paymentLabels = @json($paymentMethods);
            const manualAddressFields = [...document.querySelectorAll('[data-manual-address-field]')];
            const sections = [...document.querySelectorAll('[data-checkout-step]')];
            const indicators = [...document.querySelectorAll('[data-step-indicator]')];
            let currentStep = Number(form?.dataset.initialStep || 1);

            const hasSavedAddressSelected = () => {
                const selectedAddress = document.querySelector('input[name="address_id"]:checked');

                return Boolean(selectedAddress?.value);
            };

            const updateManualAddressRequired = () => {
                const shouldRequireManualAddress = ! hasSavedAddressSelected();

                manualAddressFields.forEach((field) => {
                    field.required = shouldRequireManualAddress;
                });
            };

            const showStep = (step) => {
                currentStep = step;

                sections.forEach((section) => {
                    section.classList.toggle('hidden', Number(section.dataset.checkoutStep) !== step);
                });

                indicators.forEach((indicator) => {
                    const active = Number(indicator.dataset.stepIndicator) === step;
                    indicator.classList.toggle('bg-zinc-900', active);
                    indicator.classList.toggle('text-white', active);
                    indicator.classList.toggle('text-zinc-600', ! active);
                });
            };

            const updatePaymentUi = () => {
                const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value;

                qrPanel?.classList.toggle('hidden', selectedPayment !== '{{ \App\Models\Order::PAYMENT_BANK_TRANSFER }}');

                if (paymentSummary) {
                    paymentSummary.textContent = paymentLabels[selectedPayment] || 'Chưa chọn';
                }
            };

            document.querySelectorAll('input[name="address_id"]').forEach((input) => {
                input.addEventListener('change', updateManualAddressRequired);
            });

            document.querySelectorAll('input[name="payment_method"]').forEach((input) => {
                input.addEventListener('change', updatePaymentUi);
            });

            document.querySelector('[data-next-step]')?.addEventListener('click', () => {
                updateManualAddressRequired();

                if (form.reportValidity()) {
                    showStep(2);
                }
            });

            document.querySelector('[data-prev-step]')?.addEventListener('click', () => {
                showStep(1);
            });

            form?.addEventListener('submit', (event) => {
                updateManualAddressRequired();

                if (currentStep === 1) {
                    event.preventDefault();

                    if (form.reportValidity()) {
                        showStep(2);
                    }
                }
            });

            updateManualAddressRequired();
            updatePaymentUi();
            showStep(currentStep);
        })();
    </script>
@endsection
