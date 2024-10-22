<x-filament-panels::page>
    <x-filament-panels::form id="form" wire:key="{{ 'forms.' . $this->getFormStatePath() }}">
        {{ $this->form }}
    </x-filament-panels::form>
    <!-- Button to toggle history display -->
    <div class="my-4">


        {{-- Display the selected check-in details --}}
        @if ($selectedCheckIn)
                <div class="max-w-7xl" x-data="{
                pricePerNight: {{ $selectedCheckIn->price_per_night ?? 0 }},
                totalAmount: 0, // Will be dynamically calculated
                advancePayment: {{ $selectedCheckIn->paid_amount ?? 0 }},
                restaurantCharge: {{ $selectedCheckIn->restaurant_bill ?? 0 }},
                laundryCharge: {{ $selectedCheckIn->restaurant_bill ?? 0 }},
                carHireCharge: {{ $selectedCheckIn->restaurant_bill ?? 0 }},
                additionalCharges: 0,
                discountPercentage: 0,
                discountAmount: 0,
                dueAmount: 0,
                payableAmount: 0,
                remainingAmount: 0,
                changeAmount: 0,
                amountPaying: 0,
                checkoutDate: '{{ \Carbon\Carbon::parse($selectedCheckIn->check_out_time)->format('Y-m-d') }}',  // Default checkout date
                initialCheckoutDate: '{{ \Carbon\Carbon::parse($selectedCheckIn->check_out_time)->format('Y-m-d') }}',  // Store initial planned checkout date
                checkinDate: '{{ \Carbon\Carbon::parse($selectedCheckIn->check_in_time)->format('Y-m-d') }}',
                totalDays: 0,
                checkoutStatus: '',

                // Initialize calculations
                init() {
                    this.calculateTotalDays();
                    this.calculatePayableAmount();
                    this.checkCheckoutStatus();
                },

                // Calculate total days between check-in and selected check-out
                calculateTotalDays() {
                    const checkin = new Date(this.checkinDate);
                    const checkout = new Date(this.checkoutDate);
                    const timeDiff = Math.abs(checkout - checkin);
                    this.totalDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                },

                // Determine if it's early, late, or on-time check-out
                checkCheckoutStatus() {
                    const plannedCheckout = new Date(this.initialCheckoutDate);
                    const selectedCheckout = new Date(this.checkoutDate);

                    if (selectedCheckout < plannedCheckout) {
                        this.checkoutStatus = 'Early Check-out';
                    } else if (selectedCheckout > plannedCheckout) {
                        this.checkoutStatus = 'Late Check-out';
                    } else {
                        this.checkoutStatus = 'On-time Check-out';
                    }
                },

                // Recalculate everything when the checkout date is updated
                updateCheckoutDate() {
                    this.calculateTotalDays();
                    this.checkCheckoutStatus();
                    this.calculatePayableAmount();  // If needed, recalculate any charges
                },


                // Calculate total after discount (including additional charges)
                calculateTotalAfterDiscount() {
                    const totalAfterDiscount = (this.totalAmount - parseFloat(this.discountAmount)) + 
                                                (parseFloat(this.restaurantCharge) + 
                                                 parseFloat(this.laundryCharge) + 
                                                 parseFloat(this.carHireCharge) + 
                                                 parseFloat(this.additionalCharges));
                    return totalAfterDiscount.toFixed(2);
                },

                // Calculate total charges
                calculateTotalCharges() {
                    return (parseFloat(this.restaurantCharge) + parseFloat(this.laundryCharge) + parseFloat(this.carHireCharge)).toFixed(2);
                },

                // Calculate discount
                calculateDiscount() {
                    this.discountAmount = (this.totalAmount * (this.discountPercentage / 100)).toFixed(2);
                    this.calculatePayableAmount();
                },

           
                   // Calculate total days between check-in and selected check-out
    calculateTotalDays() {
        const checkin = new Date(this.checkinDate);
        const checkout = new Date(this.checkoutDate);
        const timeDiff = Math.abs(checkout - checkin);
        this.totalDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    },

          // Calculate payable amount based on totalDays and other factors
              calculatePayableAmount() {
                      this.totalAmount = this.pricePerNight * this.totalDays;  // Dynamic total amount calculation

                              // You can call other calculations (e.g., for discounts, etc.)
                   const totalAfterDiscount = this.totalAmount - this.discountAmount;
             const totalAfterAdditionalCharges = totalAfterDiscount + parseFloat(this.restaurantCharge) + parseFloat(this.laundryCharge) + parseFloat(this.carHireCharge) + parseFloat(this.additionalCharges);

                  this.payableAmount = totalAfterAdditionalCharges.toFixed(2);
                  this.dueAmount = (this.payableAmount - this.advancePayment).toFixed(2);

                   if (this.dueAmount < 0) {
                    this.dueAmount = 0;
               }
                },

                // Calculate remaining payment and change
                calculatePayment() {
                    this.remainingAmount = (this.dueAmount - this.amountPaying).toFixed(2);
                    if (this.amountPaying > this.dueAmount) {
                        this.changeAmount = (this.amountPaying - this.dueAmount).toFixed(2);
                        this.remainingAmount = 0;
                    } else {
                        this.changeAmount = 0;
                    }
                }
            }" x-init="init">



                    <!-- Top Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <x-filament::section class="mb-4" icon="heroicon-m-user" icon-color="success">
                            <x-slot name="heading">
                                Guest Details
                            </x-slot>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Guest Name:</span>
                                    <span> {{ $selectedCheckIn->guest_name }} </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Room Number:</span>
                                    <span> {{ $selectedCheckIn->room_number }} </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Reservation ID:</span>
                                    <span> {{ $selectedCheckIn->reservation_number }} </span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="font-medium">Mobile No:</span>
                                    <span> {{ $selectedCheckIn->guest_phone }} </span>
                                </div>

                            </div>

                        </x-filament::section>
                        <x-filament::section class="mb-4" icon="heroicon-m-document-text" icon-color="success">
                            <x-slot name="heading">
                                Reservation Summary
                            </x-slot>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Check-in Date:</span>
                                    <span>{{ \Carbon\Carbon::parse($selectedCheckIn->check_in_time)->format('d M, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium ">Check-out Date:</span>
                                    <input type="date" x-model="checkoutDate" @change="calculateTotalDays"
                                        class="border rounded p-2 dark:bg-gray-800" />
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Total Days:</span>
                                    <span x-text="totalDays"></span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="font-medium">Checkout Status:</span>
                                    <span x-text="checkoutStatus"></span>
                                </div>
                            </div>
                        </x-filament::section>

                    </div>

                    <div class="mb-4">
                        <h2 class="text-lg font-semibold mb-4">Room Details</h2>
                        <div class="overflow-x-auto">
                            <x-table class="w-full table-fixed border border-gray-200 shadow-md">
                                <!-- Table Header -->
                                <x-table-header class="bg-gray-100 dark:bg-gray-800">
                                    <x-table-row>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            Room No.
                                        </x-table-header-cell>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            From Date
                                        </x-table-header-cell>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            To Date
                                        </x-table-header-cell>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            No. of Nights
                                        </x-table-header-cell>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            Price/Night
                                        </x-table-header-cell>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            Discount </x-table-header-cell>
                                        <x-table-header-cell class="text-gray-700 dark:text-gray-300 font-semibold p-4">
                                            Total Amount </x-table-header-cell>
                                    </x-table-row>
                                </x-table-header>

                                <!-- Table Body -->
                                <tbody>
                                    <x-table-row
                                        class="border-t hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200">
                                            {{ $selectedCheckIn->room_number }}
                                        </x-table-cell>
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200">
                                            {{ \Carbon\Carbon::parse($selectedCheckIn->check_in_time)->format('d M, Y') }}
                                        </x-table-cell>
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200">
                                            <span x-text="checkoutDate"></span>
                                        </x-table-cell>
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                            <span x-text="totalDays"></span>
                                        </x-table-cell>
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                            ₦ {{ number_format($selectedCheckIn->price_per_night ?? 0, 2) }}
                                        </x-table-cell>
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                            <span x-text="discountAmount"></span>
                                        </x-table-cell>
                                        <x-table-cell class="p-4 text-gray-800 dark:text-gray-200 number">
                                            ₦ <span
                                                x-text="Number(totalAmount).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                                        </x-table-cell>
                                    </x-table-row>
                                </tbody>
                            </x-table>
                        </div>
                    </div>

                    <!-- Main Container -->
                    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Billing Details -->
                        <x-filament::section class="mb-4" icon="heroicon-m-home-modern" icon-color="success">
                            <x-slot name="heading">
                                Billing Details
                            </x-slot>

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Price Per Night:</span>
                                    <span> ₦ {{ number_format($selectedCheckIn->price_per_night ?? 0, 2) }} </span>
                                </div>
                                <!-- Total Amount Display -->
                                <div class="flex justify-between">
                                    <span class="font-medium">Total Amount:</span>
                                    <span> ₦ <span x-text="totalAmount"></span> </span>
                                </div>
                                <!-- Discount Input -->
                                <div class="flex justify-between">
                                    <span class="font-medium">Discount:</span>
                                    <input type="number" class="input input-bordered w-24 dark:bg-gray-800" x-model="discountPercentage"
                                        x-on:input="calculateDiscount()" placeholder="%" />
                                </div>


                                <!-- Discount Amount Display -->
                                <div class="flex justify-between">
                                    <span class="font-medium">Discount Amount:</span>
                                    <span> ₦ <span x-text="discountAmount"></span> </span>
                                </div>
                                <!-- Payable Amount Display -->
                                <div class="flex justify-between">
                                    <span class="font-medium">Total After Discount:</span>
                                    <span> ₦ <span x-text="payableAmount"></span> </span>
                                </div>



                                <!-- Advance Amount Display -->
                                <div class="flex justify-between">
                                    <span class="font-medium">Advance Payment:</span>
                                    <span>₦ {{ number_format($advancePayment, 2) }}</span>
                                </div>


                                <!-- Due Amount Display -->
                                <div class="flex justify-between">
                                    <span class="font-medium">Due Amount:</span>
                                    <span> ₦ <span x-text="dueAmount"></span> </span>
                                </div>
                            </div>
                        </x-filament::section>

                        <!-- Additional Charges and Payment Details -->
                        <x-filament::section class="mb-4" icon="heroicon-m-currency-dollar" icon-color="success">
                            <x-slot name="heading">
                                Additional Charges
                            </x-slot>

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Additional Charges:</span>
                                    <input type="number" class="input input-bordered w-24 dark:bg-gray-800" x-model="additionalCharges"
                                        x-on:input="calculatePayableAmount()" />
                                </div>


                            </div>

                            <!-- Payments Details -->
                            <h2 class="text-lg font-semibold mt-6 mb-4">Payments Details</h2>
                            <div class="space-y-2">


                                <div class="flex justify-between">
                                    <span class="font-medium">Due Amount (Updated):</span>
                                    <span>₦ <span x-text="dueAmount"></span></span>
                                </div>
                            </div>
                        </x-filament::section>

                        <!-- Room Posted Bill -->
                        <x-filament::section class="mb-4" icon="heroicon-m-receipt-percent" icon-color="success">
                            <x-slot name="heading">
                                Room Posted Bill
                            </x-slot>

                            <table class="w-full text-left table-auto">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-800">
                                        <th class="p-4">Bill Type</th>
                                        <th class="p-4">Total (₦)</th>
                                        <th class="p-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t ">
                                        <td class="p-4">Restaurant</td>
                                        <td class="p-4">₦ <span x-text="restaurantCharge"></span></td>
                                        <td class="p-4">
                                            <x-filament::button
                                                class="bg-green-500 text-white px-4 py-2 rounded">Print</x-filament::button>
                                        </td>
                                    </tr>
                                    <tr class="border-t">
                                        <td class="p-4">Laundry</td>
                                        <td class="p-4">₦ <span x-text="laundryCharge"></span></td>
                                        <td class="p-4">
                                            <x-filament::button
                                                class="bg-green-500 text-white px-4 py-2 rounded">Print</x-filament::button>
                                        </td>
                                    </tr>
                                    <tr class="border-t">
                                        <td class="p-4">Car Hire</td>
                                        <td class="p-4">₦ <span x-text="carHireCharge"></span></td>
                                        <td class="p-4">
                                            <x-filament::button
                                                class="bg-green-500 text-white px-4 py-2 rounded">Print</x-filament::button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-200 border-t font-bold dark:bg-gray-800">
                                        <td class="p-4">Total</td>
                                        <td class="p-4">₦ <span x-text="calculateTotalCharges()"></span></td>
                                        <td class="p-4"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </x-filament::section>


                        <!-- Credit Section Here -->
                        <x-filament::section class="mb-4" icon="heroicon-m-credit-card" icon-color="success">
                            <x-slot name="heading">
                                Credit
                            </x-slot>
                            <!-- Payment Method Select -->
                            <div class="mb-4">
                                <label for="payment-method" class="block text-sm font-medium mb-2">Payment Method</label>
                                <select id="payment-method" class="block w-full p-2 border rounded dark:bg-gray-800">
                                    <option value="cash">Cash</option>
                                    <option value="credit-card">Card</option>
                                    <option value="bank-transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <!-- Amount Input -->
                            <div class="flex justify-between mb-4">
                                <span class="font-medium">Amount Paying:</span>
                                <input type="number" class="input input-bordered w-24 dark:bg-gray-800" x-model="amountPaying"
                                    x-on:input="calculatePayment()" />
                            </div>

                            <!-- Balance Details Section -->
                            <x-filament::card class="bg-gray-100 rounded-lg p-4 mt-6">
                                <x-slot name="heading">
                                    Balance Details
                                </x-slot>

                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Remaining Amount:</span>
                                        <span>₦ <span x-text="remainingAmount"></span></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Change:</span>
                                        <span>₦ <span x-text="changeAmount"></span></span>
                                    </div>
                                </div>
                            </x-filament::card>
                        </x-filament::section>

                    </div>
                    <div class="justify-end mt-4 space-x-8">

                        <x-filament::button
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full shadow-md hover:shadow-lg transition-shadow"
                            x-on:click="
                                                                    $wire.set('pricePerNight', pricePerNight);
                                                                    $wire.set('totalAmount', totalAmount);
                                                                    $wire.set('advancePayment', advancePayment);
                                                                    $wire.set('restaurantCharge', restaurantCharge);
                                                                    $wire.set('laundryCharge', laundryCharge);
                                                                    $wire.set('carHireCharge', carHireCharge);
                                                                    $wire.set('additionalCharges', additionalCharges);
                                                                    $wire.set('discountPercentage', discountPercentage);
                                                                    $wire.set('discountAmount', discountAmount);
                                                                    $wire.set('dueAmount', dueAmount);
                                                                    $wire.set('payableAmount', payableAmount);
                                                                    $wire.set('remainingAmount', remainingAmount);
                                                                    $wire.set('changeAmount', changeAmount);
                                                                    $wire.set('amountPaying', amountPaying);
                                                                    $wire.checkOut()">
                            Check Out
                        </x-filament::button>
                    </div>


                </div>
        @endif



</x-filament-panels::page>