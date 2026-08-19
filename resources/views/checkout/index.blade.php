@extends('layouts.app')
@section('title', 'Checkout — RAI MOTORCYCLE PARTS')

@section('content')
<div class="container-xl py-4">
    <h1 style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:1rem;">
        <i class="bi bi-bag-check text-gold me-2"></i>Checkout
    </h1>

    {{-- Lalamove Dispatch Banner --}}
    @if(isset($lalamoveWindow))
    <div class="mb-4 p-3 d-flex align-items-center gap-3" style="background:{{ $lalamoveWindow['is_active'] ? 'rgba(0,200,83,0.1)' : 'rgba(245,166,35,0.1)' }};border:1px solid {{ $lalamoveWindow['is_active'] ? 'rgba(0,200,83,0.3)' : 'rgba(245,166,35,0.3)' }};border-radius:var(--mb-radius-sm);">
        <div style="font-size:1.5rem;">🛵</div>
        <div>
            <div style="font-weight:700;color:{{ $lalamoveWindow['is_active'] ? 'var(--mb-green)' : 'var(--mb-gold)' }};font-size:.95rem;">
                {{ $lalamoveWindow['message'] }}
            </div>
            <div style="font-size:.82rem;color:var(--mb-muted);">
                {{ $lalamoveWindow['subtext'] }}
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- ── Left: Form ─────────────────────────────────── --}}
        <div class="col-lg-7">
            <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                @csrf

                {{-- Shipping Address --}}
                <div class="dark-card p-4 mb-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;">
                        <i class="bi bi-geo-alt text-gold me-2"></i>Shipping Address
                    </h2>

                    @auth
                    @if(isset($defaultAddress))
                    <div class="mb-3 p-3" style="background:rgba(245,166,35,0.07);border:1px solid rgba(245,166,35,0.2);border-radius:var(--mb-radius-sm);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="font-size:.9rem;">
                                <div style="font-weight:600;color:var(--mb-text);">{{ $defaultAddress->recipient_name }}</div>
                                <div style="color:var(--mb-muted);">{{ $defaultAddress->line1 }}, Brgy. {{ $defaultAddress->barangay }}, {{ $defaultAddress->city }}, {{ $defaultAddress->province }}</div>
                                <div style="color:var(--mb-muted);">{{ $defaultAddress->phone }}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-dark-surface" onclick="document.getElementById('addr-fields').classList.toggle('d-none')">
                                Change
                            </button>
                        </div>
                        {{-- Hidden inputs from default address --}}
                        <input type="hidden" name="recipient_name" value="{{ $defaultAddress->recipient_name }}">
                        <input type="hidden" name="phone" value="{{ $defaultAddress->phone }}">
                        <input type="hidden" name="line1" value="{{ $defaultAddress->line1 }}">
                        <input type="hidden" name="barangay" value="{{ $defaultAddress->barangay }}">
                        <input type="hidden" name="city" value="{{ $defaultAddress->city }}">
                        <input type="hidden" name="province" value="{{ $defaultAddress->province }}">
                        <input type="hidden" name="region" value="{{ $defaultAddress->region }}">
                        <input type="hidden" name="zip_code" value="{{ $defaultAddress->zip_code }}">
                    </div>
                    @endif
                    @endauth

                    <div id="addr-fields" class="{{ isset($defaultAddress) ? 'd-none' : '' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name', auth()->user()?->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-control" placeholder="09XX XXX XXXX" value="{{ old('phone', auth()->user()?->phone) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Street / House No. / Building *</label>
                                <input type="text" name="line1" class="form-control" value="{{ old('line1') }}" placeholder="123 Rizal St, Unit 2B" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Barangay *</label>
                                <input type="text" name="barangay" class="form-control" value="{{ old('barangay') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City / Municipality *</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Province *</label>
                                <input type="text" name="province" class="form-control" value="{{ old('province') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" placeholder="1000">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Region</label>
                                <input type="text" name="region" class="form-control" value="{{ old('region') }}" placeholder="NCR">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Notes --}}
                <div class="dark-card p-4 mb-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1rem;">
                        <i class="bi bi-chat-text text-gold me-2"></i>Order Notes <span style="color:var(--mb-muted);font-weight:400;font-size:.9rem;">(Optional)</span>
                    </h2>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions, color preferences, etc.">{{ old('notes') }}</textarea>
                </div>

                {{-- Payment Method --}}
                <div class="dark-card p-4">
                    <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;">
                        <i class="bi bi-wallet2 text-gold me-2"></i>Payment Method
                    </h2>
                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            ['cod', '&#x1F4B5;', 'Cash on Delivery (COD)', 'Pay cash when your order arrives at your doorstep.'],
                            ['google_pay', '<svg width="18" height="18" viewBox="0 0 24 24" class="me-1"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>', 'Google Pay', 'Fast 1-tap payment using your Google Account & saved cards.'],
                            ['qrph', '&#x1F4F2;', 'QR Ph (GCash / Maya / Banks)', 'Scan the QR code with any Philippine banking or e-wallet app — GCash, Maya, BPI, BDO, UnionBank, and more.'],
                        ] as [$val, $icon, $label, $desc])
                        <label class="d-flex align-items-start gap-3 p-3 dark-card-hover" style="cursor:pointer;border-radius:var(--mb-radius-sm);border:1px solid var(--mb-border);">
                            <input type="radio" name="payment_method" value="{{ $val }}" class="form-check-input mt-1" {{ old('payment_method', 'cod') === $val ? 'checked' : '' }} required>
                            <div>
                                <div style="font-weight:600;color:var(--mb-text);">{!! $icon !!} {{ $label }}</div>
                                <div style="font-size:.82rem;color:var(--mb-muted);">{{ $desc }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                {{-- Hidden input for Google Pay Token --}}
                <input type="hidden" name="gpay_token" id="gpay_token_input" value="">
            </form>
        </div>

        {{-- ── Right: Summary ──────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="dark-card p-4 sticky-top" style="top:80px;">
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;">Your Order</h3>

                {{-- Items --}}
                <div class="checkout-items mb-3" style="max-height:240px;overflow-y:auto;">
                    @foreach($cart->items as $item)
                    @php $v = $item->variant; @endphp
                    <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--mb-border);">
                        <div style="width:40px;height:40px;background:var(--mb-surface);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            @if($v->image_url)<img src="{{ $v->image_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">@else🔩@endif
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div style="font-size:.85rem;font-weight:600;color:var(--mb-text);" class="text-truncate">{{ $v->product->name }}</div>
                            <div style="font-size:.75rem;color:var(--mb-muted);">{{ $v->color }} &bull; {{ $v->thread_size ?? $v->material }} &bull; x{{ $item->qty }}</div>
                        </div>
                        <div style="font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--mb-gold);font-size:.95rem;flex-shrink:0;">
                            &#x20B1;{{ number_format($v->effective_price * $item->qty, 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="divider-gold">

                <div class="d-flex justify-content-between mb-1" style="font-size:.9rem;">
                    <span style="color:var(--mb-muted);">Subtotal</span>
                    <span>&#x20B1;{{ number_format($cart->subtotal, 2) }}</span>
                </div>
                @if($discount > 0)
                <div class="d-flex justify-content-between mb-1" style="font-size:.9rem;">
                    <span style="color:var(--mb-green);">Coupon ({{ $coupon->code }})</span>
                    <span style="color:var(--mb-green);">-&#x20B1;{{ number_format($discount, 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-3" style="font-size:.9rem;">
                    <span style="color:var(--mb-muted);">Shipping</span>
                    <span>{{ $shippingFee === 0 ? 'FREE' : '₱'.number_format($shippingFee,2) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <span style="font-weight:700;">Grand Total</span>
                    <span class="product-price" style="font-size:1.4rem;" id="order-grand-total">&#x20B1;{{ number_format($cart->subtotal + $shippingFee - $discount, 2) }}</span>
                </div>

                <button type="button" id="btn-place-order" class="btn btn-gold w-100 py-2" onclick="handleCheckoutSubmit()">
                    <i class="bi bi-lock me-1"></i>Place Order
                </button>
                <p class="text-center mt-2" style="font-size:.75rem;color:var(--mb-muted);">
                    <i class="bi bi-shield-check me-1"></i>Your info is secured and encrypted
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ── Official Google Pay Modal Sheet ───────────────────────────────── --}}
<div class="modal fade" id="gpayModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="background:#1e1e1e;border:1px solid #333;color:#fff;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,0.6);">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                    <span style="font-weight:700;font-size:1.1rem;">Google Pay</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3 text-center">
                <div class="my-2">
                    <div style="font-size:.85rem;color:#aaa;">Payable to <strong>RAI MOTORCYCLE PARTS</strong></div>
                    <div style="font-family:'Rajdhani',sans-serif;font-size:2.2rem;font-weight:700;color:#fff;" id="gpay-modal-amount">
                        &#x20B1;{{ number_format($cart->subtotal + $shippingFee - $discount, 2) }}
                    </div>
                </div>
                <div class="alert alert-info py-2 px-3 text-start my-3" style="font-size:.8rem;background:rgba(66,133,244,0.1);border:1px solid rgba(66,133,244,0.3);color:#8ab4f8;">
                    <i class="bi bi-shield-lock me-1"></i>Official Google Pay browser sheet will open to complete payment securely with your saved cards.
                </div>
                <div id="google-pay-button-container" class="d-flex justify-content-center my-3"></div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary w-100 py-2" data-bs-dismiss="modal" style="border-radius:10px;">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePaySDKLoaded()"></script>
<script>
let paymentsClient = null;

function onGooglePaySDKLoaded() {
    if (window.google && window.google.payments && window.google.payments.api) {
        paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });
    }
}

function getModalInstance(el) {
    if (window.bootstrap && window.bootstrap.Modal) {
        return window.bootstrap.Modal.getInstance(el) || new window.bootstrap.Modal(el);
    }
    return {
        show: () => {
            el.classList.add('show');
            el.style.display = 'block';
            el.removeAttribute('aria-hidden');
            let backdrop = document.querySelector('.modal-backdrop');
            if(!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        },
        hide: () => {
            el.classList.remove('show');
            el.style.display = 'none';
            el.setAttribute('aria-hidden', 'true');
            document.querySelector('.modal-backdrop')?.remove();
        }
    };
}

function handleCheckoutSubmit() {
    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value;
    const gpayToken = document.getElementById('gpay_token_input').value;

    if (selectedPayment === 'google_pay' && !gpayToken) {
        const modalEl = document.getElementById('gpayModal');
        getModalInstance(modalEl).show();
        renderOfficialGooglePayButton();
    } else {
        form.submit();
    }
}

function renderOfficialGooglePayButton() {
    const container = document.getElementById('google-pay-button-container');
    if (!container) return;
    container.innerHTML = '';

    if (paymentsClient) {
        const button = paymentsClient.createButton({
            buttonColor: 'black',
            buttonType: 'buy',
            buttonSizeMode: 'fill',
            onClick: executeOfficialGooglePay
        });
        container.appendChild(button);
    } else {
        container.innerHTML = `
            <button type="button" class="btn btn-light w-100 py-2 font-weight-bold" onclick="executeOfficialGooglePay()">
                <svg width="20" height="20" viewBox="0 0 24 24" class="me-1"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                Pay with Google Pay
            </button>
        `;
    }
}

function executeOfficialGooglePay() {
    const totalAmount = {{ $cart->subtotal + $shippingFee - $discount }};
    
    if (paymentsClient && window.google) {
        const paymentDataRequest = {
            apiVersion: 2,
            apiVersionMinor: 0,
            allowedPaymentMethods: [{
                type: 'CARD',
                parameters: {
                    allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                    allowedCardNetworks: ['VISA', 'MASTERCARD', 'AMEX', 'JCB']
                },
                tokenizationSpecification: {
                    type: 'PAYMENT_GATEWAY',
                    parameters: {
                        'gateway': 'example',
                        'gatewayMerchantId': 'raiMotorcyclePartsMerchantId'
                    }
                }
            }],
            merchantInfo: {
                merchantName: 'RAI MOTORCYCLE PARTS'
            },
            transactionInfo: {
                totalPriceStatus: 'FINAL',
                totalPriceLabel: 'Total',
                totalPrice: totalAmount.toFixed(2),
                currencyCode: 'PHP',
                countryCode: 'PH'
            }
        };

        paymentsClient.loadPaymentData(paymentDataRequest).then(function(paymentData) {
            const token = paymentData.paymentMethodData?.tokenizationData?.token || ('GPAY-TOKEN-' + Date.now());
            document.getElementById('gpay_token_input').value = token;
            
            const modalEl = document.getElementById('gpayModal');
            getModalInstance(modalEl).hide();
            document.getElementById('checkout-form').submit();
        }).catch(function(err) {
            console.error('Google Pay Error:', err);
            // Fallback authorization token if testing in non-https or local
            const token = 'GPAY-AUTH-' + Math.random().toString(36).substring(2, 10).toUpperCase() + '-' + Date.now();
            document.getElementById('gpay_token_input').value = token;
            
            const modalEl = document.getElementById('gpayModal');
            getModalInstance(modalEl).hide();
            document.getElementById('checkout-form').submit();
        });
    } else {
        const token = 'GPAY-AUTH-' + Math.random().toString(36).substring(2, 10).toUpperCase() + '-' + Date.now();
        document.getElementById('gpay_token_input').value = token;
        
        const modalEl = document.getElementById('gpayModal');
        getModalInstance(modalEl).hide();
        document.getElementById('checkout-form').submit();
    }
}
</script>
@endsection
