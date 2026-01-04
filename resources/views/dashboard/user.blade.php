<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-lg shadow-lg animate-pulse-slow">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Account Status</h2>
                    <p class="text-sm text-gray-500">Shop under review</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in">
                <!-- Top Gradient Section -->
                <div class="bg-gradient-to-br from-yellow-400 via-orange-400 to-amber-500 px-8 py-12 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-20">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="1" fill="white"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#dots)"/>
                        </svg>
                    </div>
                    <div class="relative">
                        <!-- Animated Clock Icon -->
                        <div class="mx-auto h-24 w-24 rounded-full bg-white/20 backdrop-blur flex items-center justify-center mb-6 animate-bounce-slow">
                            <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                                <svg class="h-12 w-12 text-yellow-500 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-2">Shop Under Review</h2>
                        <p class="text-yellow-100 text-lg">Your registration is being processed</p>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="px-8 py-8">
                    <!-- Status Badge -->
                    <div class="flex justify-center mb-6">
                        <span class="inline-flex items-center px-6 py-3 rounded-full text-sm font-bold bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 shadow-sm">
                            <span class="relative flex h-3 w-3 mr-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                            </span>
                            Pending Approval
                        </span>
                    </div>

                    <!-- Message -->
                    <div class="text-center mb-8">
                        <p class="text-gray-600 leading-relaxed">
                            Your shop registration is currently being reviewed by our team. 
                            You will be notified once your account has been approved.
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-yellow-200 rounded-2xl p-5 mb-8">
                        <div class="flex items-start">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center flex-shrink-0">
                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-yellow-800">Review Time</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    This process typically takes <strong>1-2 business days</strong>. 
                                    Please ensure your contact information is accurate.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Details -->
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Shop Details</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Shop Name
                                </span>
                                <span class="font-semibold text-gray-900">{{ $shop->name ?? 'Not registered' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Status
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                    {{ ucfirst($shop->status ?? 'pending') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Submitted
                                </span>
                                <span class="font-semibold text-gray-900">{{ $shop?->created_at?->diffForHumans() ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 pb-8">
                    <div class="border-t border-gray-100 pt-6 text-center">
                        <p class="text-sm text-gray-500">
                            Need help? Contact us at 
                            <a href="mailto:support@shopledger.com" class="text-indigo-600 hover:text-indigo-800 font-medium">support@shopledger.com</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="mt-8 px-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Review Progress</span>
                    <span class="text-xs font-medium text-gray-500">Step 2 of 3</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full animate-progress" style="width: 66%"></div>
                </div>
                <div class="flex justify-between mt-2">
                    <div class="flex items-center">
                        <div class="h-6 w-6 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xs ml-2 text-gray-600">Submitted</span>
                    </div>
                    <div class="flex items-center">
                        <div class="h-6 w-6 rounded-full bg-yellow-500 flex items-center justify-center animate-pulse">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs ml-2 text-yellow-600 font-medium">Review</span>
                    </div>
                    <div class="flex items-center">
                        <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-xs ml-2 text-gray-400">Approved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes progress {
            from { width: 0%; }
            to { width: 66%; }
        }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        .animate-bounce-slow { animation: bounce-slow 2s ease-in-out infinite; }
        .animate-spin-slow { animation: spin-slow 8s linear infinite; }
        .animate-pulse-slow { animation: pulse-slow 3s ease-in-out infinite; }
        .animate-progress { animation: progress 1.5s ease-out forwards; }
    </style>
</x-app-layout>
