@extends('layouts.public')

@section('title', 'Booking — '.$service->name)

@section('content')
@php
    $isPackage = request()->has('package') && request()->has('services');
    $packageServices = collect();
    $totalPrice = $service->price;
    $totalDuration = $service->duration_minutes;

    if ($isPackage) {
        $serviceIds = explode(',', request('services'));
        $extraIds = array_filter($serviceIds, fn($id) => $id != $service->id);
        if (!empty($extraIds)) {
            $packageServices = \App\Models\Service::whereIn('id', $extraIds)->get();
            $totalPrice += $packageServices->sum('price');
            $totalDuration += $packageServices->sum('duration_minutes');
        }
    }
@endphp

<section class="relative min-h-[100svh] flex flex-col justify-start overflow-hidden pt-28 pb-20" style="background: #050505;">
    {{-- Single subtle glow --}}
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[500px] h-[500px] rounded-full opacity-[0.03] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(100px);"></div>

    <div class="relative z-10 w-full max-w-3xl mx-auto px-4 sm:px-6">
        {{-- Back --}}
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-amber-400 transition-colors mb-6 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7 7l-7-7 7-7"/></svg>
            <span class="text-xs font-semibold tracking-wide">Back</span>
        </a>

        {{-- Service Header --}}
        <div class="border border-zinc-800 rounded-2xl p-6 sm:p-7 mb-8 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-2">
                    @if($isPackage)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[9px] font-bold uppercase tracking-widest">✦ Custom Package</span>
                        <h1 class="text-xl sm:text-2xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">
                            {{ $service->name }} <span class="text-zinc-600 text-lg font-light italic">&amp; more</span>
                        </h1>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="text-[9px] bg-zinc-900 text-zinc-400 border border-zinc-800 px-2 py-0.5 rounded font-semibold">{{ $service->name }} ({{ $service->duration_minutes }}m)</span>
                            @foreach($packageServices as $ps)
                                <span class="text-[9px] bg-zinc-900 text-zinc-500 border border-zinc-800 px-2 py-0.5 rounded font-semibold">{{ $ps->name }} ({{ $ps->duration_minutes }}m)</span>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-3 text-[9px] font-bold uppercase tracking-wider text-zinc-600">
                            <span>{{ $service->duration_minutes }} min</span>
                            <span class="w-px h-3 bg-zinc-800"></span>
                            <span>{{ $service->stylists->count() }} stylist</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">{{ $service->name }}</h1>
                        @if($service->description)
                            <p class="text-zinc-500 text-sm max-w-lg leading-relaxed">{{ $service->description }}</p>
                        @endif
                    @endif
                </div>
                <div class="shrink-0 text-right">
                    <span class="text-[9px] text-zinc-600 uppercase tracking-widest font-bold block">{{ $isPackage ? 'Total' : 'Price' }}</span>
                    <span class="text-2xl font-black gradient-text mt-0.5 block">{{ $service->formattedPrice() }}</span>
                    @if($isPackage)
                        <span class="text-[10px] text-zinc-500 mt-0.5 block">{{ $totalDuration }} min total</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mb-6 bg-red-500/8 border border-red-500/15 text-red-200/80 px-5 py-4 rounded-2xl text-xs">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-2">⚠ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Reservation Dossier --}}
        <div class="border border-zinc-800 rounded-2xl p-6 sm:p-8 relative">
            {{-- Brass corner brackets --}}
            <div class="absolute top-3 left-3 w-4 h-4 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>
            <div class="absolute top-3 right-3 w-4 h-4 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-right: 1px solid rgba(245, 158, 11, 0.2);"></div>
            <div class="absolute bottom-3 left-3 w-4 h-4 pointer-events-none" style="border-bottom: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>
            <div class="absolute bottom-3 right-3 w-4 h-4 pointer-events-none" style="border-bottom: 1px solid rgba(245, 158, 11, 0.2); border-right: 1px solid rgba(245, 158, 11, 0.2);"></div>

            <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingForm()" x-init="init()" x-on:submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                {{-- Step Progress — brass spine style --}}
                <div class="flex items-center gap-3 sm:gap-6 mb-10 pb-6 border-b border-zinc-800/60">
                    <template x-for="(label, i) in ['Stylist', 'Schedule', 'Details']" :key="i">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all duration-400 border"
                                :class="step === i + 1 ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/20' : (step > i + 1 ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-zinc-900 border-zinc-800 text-zinc-600')">
                                <template x-if="step > i + 1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="step <= i + 1">
                                    <span x-text="'0' + (i + 1)"></span>
                                </template>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider hidden sm:inline"
                                :class="step === i + 1 ? 'text-white' : 'text-zinc-600'" x-text="label"></span>
                            <div x-show="i < 2" class="w-6 sm:w-12 h-px bg-zinc-800/60"></div>
                        </div>
                    </template>
                </div>

                {{-- ===== STEP 1: Stylist ===== --}}
                <div class="step-content" :class="step === 1 ? 'active' : ''" x-show="step === 1" x-transition x-cloak>
                    <h2 class="text-xl font-bold text-white mb-1" style="font-family: 'Playfair Display', serif;">01. Select Stylist</h2>
                    <p class="text-zinc-500 text-sm mb-8">Each stylist is a specialist. Choose the one that fits your needs.</p>

                    @if($stylists->isNotEmpty())
                        <div class="grid gap-3">
                            @foreach($stylists as $stylist)
                                <label class="group relative flex items-center gap-4 p-4 sm:p-5 rounded-xl border cursor-pointer transition-all duration-400"
                                    :class="selectedStylist == {{ $stylist->id }}
                                        ? 'border-amber-500/40 bg-amber-500/5'
                                        : 'border-zinc-800 hover:border-zinc-700 bg-zinc-950/30'">
                                    <input type="radio" name="stylist_id" value="{{ $stylist->id }}" class="hidden" x-model="selectedStylist">

                                    {{-- Initials --}}
                                    <div class="w-12 h-12 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center shrink-0 font-bold text-white text-base">
                                        @php
                                            $words = explode(' ', $stylist->name);
                                            $initials = '';
                                            foreach($words as $w) $initials .= strtoupper(substr($w, 0, 1));
                                            echo substr($initials, 0, 2);
                                        @endphp
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-sm text-white">{{ $stylist->name }}</p>
                                        <span class="text-[10px] text-amber-400/70 font-semibold">{{ $stylist->specialty ?? 'Master Stylist' }}</span>
                                        @if($stylist->bio)
                                            <p class="text-zinc-600 text-[11px] mt-1 leading-relaxed line-clamp-1 italic">"{{ $stylist->bio }}"</p>
                                        @endif
                                    </div>

                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-300"
                                        :class="selectedStylist == {{ $stylist->id }} ? 'border-amber-500 bg-amber-500' : 'border-zinc-700'">
                                        <svg x-show="selectedStylist == {{ $stylist->id }}" xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 border-2 border-dashed border-zinc-800 rounded-xl">
                            <p class="text-zinc-600 text-sm">No stylist available for this service.</p>
                        </div>
                    @endif
                </div>

                {{-- ===== STEP 2: Schedule ===== --}}
                <div class="step-content" :class="step === 2 ? 'active' : ''" x-show="step === 2" x-transition x-cloak>
                    <h2 class="text-xl font-bold text-white mb-1" style="font-family: 'Playfair Display', serif;">02. Pick Schedule</h2>
                    <p class="text-zinc-500 text-sm mb-8">Slots update in real-time based on stylist availability.</p>

                    <div class="space-y-6">
                        {{-- Date --}}
                        <div>
                            <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-2 block">1. Select Date</label>
                            <input type="date" name="booking_date" x-model="bookingDate"
                                min="{{ now()->timezone(config('app.timezone'))->format('Y-m-d') }}"
                                class="w-full input-dark focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all placeholder-zinc-700"
                                required>
                        </div>

                        {{-- Time slots --}}
                        <div x-show="bookingDate && selectedStylist">
                            <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-3 block flex items-center gap-2">
                                2. Select Time
                                <span x-show="isLoadingSlots" class="inline-flex items-center gap-1 text-zinc-600 font-normal tracking-normal text-[9px]">
                                    <svg class="animate-spin w-2.5 h-2.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Loading...
                                </span>
                            </label>

                            {{-- Skeleton --}}
                            <div x-show="isLoadingSlots" class="grid grid-cols-3 sm:grid-cols-5 gap-2 animate-pulse">
                                <template x-for="i in 9">
                                    <div class="h-12 bg-zinc-900 border border-zinc-800 rounded-xl"></div>
                                </template>
                            </div>

                            {{-- Lacquer tiles --}}
                            <div x-show="!isLoadingSlots" class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                @foreach(['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'] as $time)
                                    <button type="button"
                                        @click="if(!unavailableSlots.includes('{{ $time }}')) selectedTime = '{{ $time }}'"
                                        :disabled="unavailableSlots.includes('{{ $time }}')"
                                        class="h-12 rounded-xl border text-xs font-bold transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-amber-500/50"
                                        :class="selectedTime === '{{ $time }}'
                                            ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/15'
                                            : (unavailableSlots.includes('{{ $time }}')
                                                ? 'bg-zinc-950 border-zinc-800/50 text-zinc-700 cursor-not-allowed line-through'
                                                : 'bg-zinc-950 border-zinc-800 text-zinc-400 hover:border-zinc-600 hover:text-white')">
                                        <span>{{ $time }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <input type="hidden" name="scheduled_at" :value="bookingDate && selectedTime ? bookingDate + ' ' + selectedTime : ''">
                        </div>

                        <div x-show="!bookingDate || !selectedStylist" class="text-center py-10 border-2 border-dashed border-zinc-800 rounded-xl bg-zinc-950/20">
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-zinc-600 text-sm">Select a stylist & date to see available slots.</p>
                        </div>
                    </div>
                </div>

                {{-- ===== STEP 3: Details ===== --}}
                <div class="step-content" :class="step === 3 ? 'active' : ''" x-show="step === 3" x-transition x-cloak>
                    <h2 class="text-xl font-bold text-white mb-1" style="font-family: 'Playfair Display', serif;">03. Your Details</h2>
                    <p class="text-zinc-500 text-sm mb-8">Confirm your reservation details.</p>

                    {{-- Persist summary card --}}
                    <div class="border border-zinc-800 rounded-xl p-5 mb-6 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="text-xs">
                                <p class="text-zinc-300 font-semibold" x-text="selectedStylistName || 'Selected stylist'"></p>
                                <p class="text-zinc-600" x-text="bookingDate && selectedTime ? bookingDate + ' — ' + selectedTime : 'Selected time'"></p>
                            </div>
                        </div>
                        <span class="text-lg font-black gradient-text">{{ $service->formattedPrice() }}</span>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-2 block">Full Name</label>
                            <input type="text" name="customer_name" x-model="customerName" required
                                class="w-full input-dark focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all placeholder-zinc-600 @error('customer_name') !border-red-500/40 @enderror"
                                placeholder="Enter your full name">
                            @error('customer_name') <p class="text-red-400/80 text-[10px] mt-1.5 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-2 block">Email</label>
                                <input type="email" name="customer_email" x-model="customerEmail" required
                                    class="w-full input-dark focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all placeholder-zinc-600 @error('customer_email') !border-red-500/40 @enderror"
                                    placeholder="you@example.com">
                                @error('customer_email') <p class="text-red-400/80 text-[10px] mt-1.5 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-2 block">WhatsApp / Phone</label>
                                <input type="tel" name="customer_phone" x-model="customerPhone" @input.debounce.500ms="lookupCustomer()" required
                                    class="w-full input-dark focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all placeholder-zinc-600 @error('customer_phone') !border-red-500/40 @enderror"
                                    placeholder="08xxxxxxxxxx">
                                @error('customer_phone') <p class="text-red-400/80 text-[10px] mt-1.5 font-semibold">{{ $message }}</p> @enderror
                                <p x-show="lookupMessage" x-text="lookupMessage" class="text-[10px] mt-2 font-semibold" :class="isExistingCustomer ? 'text-amber-400' : 'text-emerald-400'"></p>
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-2 block">Notes <span class="font-normal text-zinc-700 tracking-normal normal-case">(optional)</span></label>
                            <textarea name="notes" rows="3"
                                class="w-full input-dark focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all resize-none placeholder-zinc-600 @error('notes') !border-red-500/40 @enderror"
                                placeholder="Special requests...">{{ old('notes', request('notes') ?? ($isPackage ? 'Custom Package: ' . $service->name . $packageServices->map(fn($s) => ' + ' . $s->name)->implode('') . ' (' . $totalDuration . 'm)' : '')) }}</textarea>
                            @error('notes') <p class="text-red-400/80 text-[10px] mt-1.5 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between mt-8 pt-5 border-t border-zinc-800/60">
                    <button type="button" x-show="step > 1" @click="step--"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-zinc-500 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>

                    <button type="button" x-show="step < 3" @click="if(validateStep()) step++"
                        class="ml-auto inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-black px-7 py-3.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-amber-500/15 active:scale-[0.97]">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <button type="submit" x-show="step === 3" :disabled="isSubmitting"
                        class="ml-auto inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-black px-7 py-3.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-amber-500/15 active:scale-[0.97]"
                        :class="isSubmitting ? 'opacity-60 cursor-not-allowed' : ''">
                        <svg x-show="!isSubmitting" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="isSubmitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="isSubmitting ? 'Processing...' : 'Confirm Booking'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function bookingForm() {
    return {
        step: 1,
        selectedStylist: null,
        bookingDate: '',
        selectedTime: '',
        isLoadingSlots: false,
        isSubmitting: false,
        unavailableSlots: [],
        customerPhone: '{{ old('customer_phone') }}',
        customerName: '{{ old('customer_name') }}',
        customerEmail: '{{ old('customer_email') }}',
        lookupMessage: '',
        isExistingCustomer: false,

        async lookupCustomer() {
            if (this.customerPhone.length < 8) {
                this.lookupMessage = '';
                return;
            }
            try {
                const res = await fetch(`/api/v1/customers/lookup?phone=${encodeURIComponent(this.customerPhone)}`);
                if (!res.ok) throw new Error('Lookup failed');
                const data = await res.json();
                if (data.found) {
                    this.customerName = data.customer.name;
                    this.customerEmail = data.customer.email;
                    this.lookupMessage = data.message;
                    this.isExistingCustomer = true;
                    if (data.customer.preferred_stylist_id && !this.selectedStylist) {
                        this.selectedStylist = data.customer.preferred_stylist_id;
                    }
                } else {
                    this.lookupMessage = data.message;
                    this.isExistingCustomer = false;
                }
            } catch (e) {
                console.error(e);
            }
        },

        get selectedStylistName() {
            @if($stylists->isNotEmpty())
                const stylists = @json($stylists->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
                const found = stylists.find(s => s.id === this.selectedStylist);
                return found ? found.name : '';
            @else
                return '';
            @endif
        },

        async checkAvailability() {
            if (!this.selectedStylist || !this.bookingDate) return;
            this.isLoadingSlots = true;
            try {
                const res = await fetch(`/api/v1/availabilities?stylist_id=${this.selectedStylist}&service_id={{ $service->id }}&date=${this.bookingDate}`);
                if (!res.ok) throw new Error('Failed');
                const data = await res.json();
                this.unavailableSlots = data.unavailable_slots || [];
                if (this.unavailableSlots.includes(this.selectedTime)) {
                    this.selectedTime = '';
                }
            } catch (e) {
                console.error(e);
                $dispatch('toast', { type: 'error', message: 'Failed to load availability.' });
            } finally {
                this.isLoadingSlots = false;
            }
        },

        validateStep() {
            if (this.step === 1 && !this.selectedStylist) {
                $dispatch('toast', { type: 'error', message: 'Please select a stylist first' });
                return false;
            }
            if (this.step === 2 && (!this.bookingDate || !this.selectedTime)) {
                $dispatch('toast', { type: 'error', message: 'Please select date & time first' });
                return false;
            }
            return true;
        },

        init() {
            this.$watch('selectedStylist', () => {
                this.selectedTime = '';
                this.checkAvailability();
            });
            this.$watch('bookingDate', () => {
                this.selectedTime = '';
                this.checkAvailability();
            });
        }
    }
}
</script>
@endsection
