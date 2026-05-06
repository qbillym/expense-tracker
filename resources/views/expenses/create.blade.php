@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card animate-fade-in">
                <div class="card-header">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add New Expense
                    </h4>
                </div>
                <div class="card-body p-4">
                    <!-- Expense Entry Method Tabs -->
                    <ul class="nav nav-tabs mb-4" id="expenseTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ old('entry_method', 'manual') === 'manual' ? 'active' : '' }}" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                                <i class="bi bi-pencil me-2"></i>Manual Entry
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ old('entry_method') === 'mobile' ? 'active' : '' }}" id="mobile-money-tab" data-bs-toggle="tab" data-bs-target="#mobile-money" type="button" role="tab">
                                <i class="bi bi-phone me-2"></i>Mobile Money
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="expenseTabsContent">
                        <!-- Manual Entry Tab -->
                        <div class="tab-pane fade {{ old('entry_method', 'manual') === 'manual' ? 'show active' : '' }}" id="manual" role="tabpanel">
                            <form id="manualForm" method="POST" action="{{ route('expenses.store') }}">
                                @csrf
                                <input type="hidden" name="entry_method" value="manual">

                                @if ($errors->any() && old('entry_method', 'manual') === 'manual')
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label fw-semibold">
                                            <i class="bi bi-tag me-1"></i>Title *
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title"
                                               value="{{ old('title') }}" placeholder="e.g., Lunch at Restaurant" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="amount" class="form-label fw-semibold">
                                            <i class="bi bi-cash me-1"></i>Amount (RWF) *
                                        </label>
                                        <input type="number" class="form-control" id="amount" name="amount"
                                               value="{{ old('amount') }}" step="0.01" min="0"
                                               placeholder="0.00" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label fw-semibold">
                                            <i class="bi bi-folder me-1"></i>Category *
                                        </label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select a category</option>
                                            @foreach(\App\Models\Expense::CATEGORIES as $categoryName => $keywords)
                                                <option value="{{ $categoryName }}"
                                                        {{ old('category') === $categoryName ? 'selected' : '' }}>
                                                    {{ $categoryName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="date" class="form-label fw-semibold">
                                            <i class="bi bi-calendar me-1"></i>Date *
                                        </label>
                                        <input type="date" class="form-control" id="date" name="date"
                                               value="{{ old('date', date('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-semibold">
                                        <i class="bi bi-sticky me-1"></i>Notes
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                              placeholder="Additional details about this expense...">{{ old('notes') }}</textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Expenses
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Save Expense
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Mobile Money Tab -->
                        <div class="tab-pane fade {{ old('entry_method') === 'mobile' ? 'show active' : '' }}" id="mobile-money" role="tabpanel">
                            <form id="mobileForm" method="POST" action="{{ route('expenses.store') }}">
                                @csrf
                                <input type="hidden" name="entry_method" value="mobile">

                                @if ($errors->any() && old('entry_method') === 'mobile')
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label for="mobile_money_message" class="form-label fw-semibold">
                                        <i class="bi bi-chat-text me-1"></i>Paste Mobile Money Message *
                                    </label>
                                    <div class="mobile-money-wrapper">
                                        <textarea class="form-control mobile-money-input" id="mobile_money_message"
                                                  name="mobile_money_message" rows="4"
                                                  placeholder="Paste your mobile money SMS here (e.g., 'You have received RWF 50,000 from JOHN DOE. New balance: RWF 150,000')">{{ old('mobile_money_message') }}</textarea>
                                        <div class="mobile-money-icon">
                                            <i class="bi bi-phone"></i>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Paste the SMS you received from your mobile money service. We'll automatically detect the amount and suggest a category.
                                    </div>
                                </div>

                                <!-- Parsed Information Display -->
                                <div id="parsedInfo" class="mb-4" style="display: {{ old('entry_method') === 'mobile' ? 'block' : 'none' }};">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-magic me-1"></i>Detected Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Detected Amount</label>
                                            <input type="number" class="form-control" id="detected_amount"
                                                   name="amount" step="0.01" readonly value="{{ old('amount') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Current Balance</label>
                                            <input type="number" class="form-control" id="detected_balance"
                                                   name="detected_balance" step="0.01" readonly value="{{ old('detected_balance') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Suggested Category</label>
                                            <select class="form-select" id="suggested_category" name="category" required>
                                                @foreach(\App\Models\Expense::CATEGORIES as $categoryName => $keywords)
                                                    <option value="{{ $categoryName }}"
                                                            {{ old('category') === $categoryName ? 'selected' : '' }}>
                                                        {{ $categoryName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Transaction Title</label>
                                            <input type="text" class="form-control" id="detected_title"
                                                   name="title" readonly value="{{ old('title', 'Mobile Money Transaction') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="mobile_notes" class="form-label fw-semibold">
                                        <i class="bi bi-sticky me-1"></i>Additional Notes
                                    </label>
                                    <textarea class="form-control" id="mobile_notes" name="notes" rows="2"
                                              placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Expenses
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Save Expense
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
// Enhanced Mobile money message parsing
const mobileMoneyMessageInput = document.getElementById('mobile_money_message');
if (mobileMoneyMessageInput) {
    mobileMoneyMessageInput.addEventListener('input', function() {
        const message = this.value.trim();
        const parsedInfo = document.getElementById('parsedInfo');

        if (message.length > 10) {
            // Enhanced amount detection patterns
            const amountPatterns = [
                /(?:RWF|UGX|USD|USD|KES|TZS|BIF|CDF)\s*([\d,]+(?:\.\d{2})?)/i,
                /([\d,]+(?:\.\d{2})?)\s*(?:RWF|UGX|USD|USD|KES|TZS|BIF|CDF)/i,
                /(?:amount|amt)\s*[:\s]*([\d,]+(?:\.\d{2})?)/i,
                /(?:paid|sent|received|spent)\s*(?:RWF|UGX|USD)?\s*([\d,]+(?:\.\d{2})?)/i,
                /(?:frw|rwf|francs?)\s*([\d,]+(?:\.\d{2})?)/i,
                /\$?\s*([\d,]+(?:\.\d{2})?)/i,
                /([\d,]+(?:\.\d{2})?)\s*(?:frw|rwf|francs?)/i
            ];

            // Enhanced balance detection patterns
            const balancePatterns = [
                /(?:balance|bal)\s*[:\s]+(?:RWF|UGX|USD|KES|TZS|BIF|CDF)?\s*([\d,]+(?:\.\d{2})?)/i,
                /(?:new|remaining|current)\s*balance\s*[:\s]*([\d,]+(?:\.\d{2})?)/i,
                /(?:available|avail)\s*[:\s]*([\d,]+(?:\.\d{2})?)/i,
                /(?:account|acc)\s*balance\s*[:\s]*([\d,]+(?:\.\d{2})?)/i
            ];

            // Enhanced merchant/recipient detection
            const merchantPatterns = [
                /to[:\s]+([^\n\r\-\.]+)/i,
                /from[:\s]+([^\n\r\-\.]+)/i,
                /paid to[:\s]+([^\n\r\-\.]+)/i,
                /sent to[:\s]+([^\n\r\-\.]+)/i,
                /received from[:\s]+([^\n\r\-\.]+)/i,
                /merchant[:\s]+([^\n\r\-\.]+)/i,
                /at[:\s]+([^\n\r\-\.]+)/i,
                /for[:\s]+([^\n\r\-\.]+)/i
            ];

            // Transaction type detection
            const isReceived = /received|got|credited|deposited/i.test(message);
            const isSent = /sent|paid|debited|withdrawn|spent/i.test(message);

            // Find amount
            let amount = null;
            for (const pattern of amountPatterns) {
                const match = message.match(pattern);
                if (match) {
                    amount = parseFloat(match[1].replace(/,/g, ''));
                    break;
                }
            }

            // Find balance
            let balance = null;
            for (const pattern of balancePatterns) {
                const match = message.match(pattern);
                if (match) {
                    balance = parseFloat(match[1].replace(/,/g, ''));
                    break;
                }
            }

            // Find merchant/recipient
            let merchant = null;
            for (const pattern of merchantPatterns) {
                const match = message.match(pattern);
                if (match) {
                    merchant = match[1].trim().split(/[\-\(]/)[0].trim();
                    break;
                }
            }

            // Set detected values
            if (amount && !isNaN(amount)) {
                document.getElementById('detected_amount').value = amount.toFixed(2);
            }

            if (balance && !isNaN(balance)) {
                document.getElementById('detected_balance').value = balance.toFixed(2);
            }

            // Generate smart title and detect category
            if (merchant) {
                let title = '';
                if (isReceived) {
                    title = `Received from ${merchant}`;
                } else if (isSent) {
                    title = `Payment to ${merchant}`;
                } else {
                    title = `Transaction with ${merchant}`;
                }
                document.getElementById('detected_title').value = title;

                // Enhanced category detection
                const merchantLower = merchant.toLowerCase();
                const categorySelect = document.getElementById('suggested_category');
                
                // Food & Dining
                if (merchantLower.includes('restaurant') || merchantLower.includes('food') ||
                    merchantLower.includes('cafe') || merchantLower.includes('hotel') ||
                    merchantLower.includes('bar') || merchantLower.includes('eating') ||
                    merchantLower.includes('pizza') || merchantLower.includes('burger')) {
                    categorySelect.value = 'Food & Dining';
                }
                // Transportation
                else if (merchantLower.includes('taxi') || merchantLower.includes('uber') ||
                          merchantLower.includes('bolt') || merchantLower.includes('transport') ||
                          merchantLower.includes('fuel') || merchantLower.includes('parking') ||
                          merchantLower.includes('bus') || merchantLower.includes('motor')) {
                    categorySelect.value = 'Transportation';
                }
                // Shopping
                else if (merchantLower.includes('shop') || merchantLower.includes('store') ||
                          merchantLower.includes('market') || merchantLower.includes('mall') ||
                          merchantLower.includes('supermarket') || merchantLower.includes('grocery') ||
                          merchantLower.includes('retail') || merchantLower.includes('boutique')) {
                    categorySelect.value = 'Shopping';
                }
                // Entertainment
                else if (merchantLower.includes('cinema') || merchantLower.includes('movie') ||
                          merchantLower.includes('game') || merchantLower.includes('entertainment') ||
                          merchantLower.includes('theater') || merchantLower.includes('concert')) {
                    categorySelect.value = 'Entertainment';
                }
                // Healthcare
                else if (merchantLower.includes('hospital') || merchantLower.includes('clinic') ||
                          merchantLower.includes('pharmacy') || merchantLower.includes('doctor') ||
                          merchantLower.includes('medical') || merchantLower.includes('health')) {
                    categorySelect.value = 'Healthcare';
                }
                // Education
                else if (merchantLower.includes('school') || merchantLower.includes('university') ||
                          merchantLower.includes('college') || merchantLower.includes('education') ||
                          merchantLower.includes('course') || merchantLower.includes('training')) {
                    categorySelect.value = 'Education';
                }
                // Utilities
                else if (merchantLower.includes('electric') || merchantLower.includes('water') ||
                          merchantLower.includes('internet') || merchantLower.includes('phone') ||
                          merchantLower.includes('utility') || merchantLower.includes('bill')) {
                    categorySelect.value = 'Utilities';
                }
                // Bills & Fees
                else if (merchantLower.includes('rent') || merchantLower.includes('loan') ||
                          merchantLower.includes('insurance') || merchantLower.includes('tax') ||
                          merchantLower.includes('fee') || merchantLower.includes('payment')) {
                    categorySelect.value = 'Bills & Fees';
                }
                // Personal Care
                else if (merchantLower.includes('salon') || merchantLower.includes('spa') ||
                          merchantLower.includes('gym') || merchantLower.includes('fitness') ||
                          merchantLower.includes('beauty') || merchantLower.includes('hair')) {
                    categorySelect.value = 'Personal Care';
                }
                // Travel
                else if (merchantLower.includes('hotel') || merchantLower.includes('travel') ||
                          merchantLower.includes('flight') || merchantLower.includes('booking') ||
                          merchantLower.includes('airbnb') || merchantLower.includes('reservation')) {
                    categorySelect.value = 'Travel';
                }
                // Gifts & Donations
                else if (merchantLower.includes('gift') || merchantLower.includes('donation') ||
                          merchantLower.includes('charity') || merchantLower.includes('church') ||
                          merchantLower.includes('mosque') || merchantLower.includes('temple')) {
                    categorySelect.value = 'Gifts & Donations';
                }
                // Business
                else if (merchantLower.includes('office') || merchantLower.includes('business') ||
                          merchantLower.includes('company') || merchantLower.includes('corporate') ||
                          merchantLower.includes('work') || merchantLower.includes('professional')) {
                    categorySelect.value = 'Business';
                }
                else {
                    categorySelect.value = 'Other';
                }
            }

            // Show parsed information
            parsedInfo.style.display = 'block';
            
            // Add visual feedback for successful parsing
            if (amount || balance || merchant) {
                parsedInfo.classList.add('border-success');
                parsedInfo.classList.remove('border-warning');
            } else {
                parsedInfo.classList.add('border-warning');
                parsedInfo.classList.remove('border-success');
            }
        } else {
            parsedInfo.style.display = 'none';
        }
    });
}

const manualAmountField = document.getElementById('amount');
if (manualAmountField) {
    manualAmountField.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        if (!isNaN(value)) {
            this.value = value.toFixed(2);
        }
    });
}
</script>