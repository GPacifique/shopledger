@props(['variant' => 'light'])

@php
    $quotes = [
        "Success is not final, failure is not fatal: it is the courage to continue that counts.",
        "The only way to do great work is to love what you do.",
        "Dream big. Start small. Act now.",
        "Every expert was once a beginner.",
        "Your limitation—it's only your imagination.",
        "Push yourself, because no one else is going to do it for you.",
        "Great things never come from comfort zones.",
        "Success doesn't just find you. You have to go out and get it.",
        "The harder you work for something, the greater you'll feel when you achieve it.",
        "Don't stop when you're tired. Stop when you're done.",
        "Wake up with determination. Go to bed with satisfaction.",
        "Little things make big days.",
        "It's going to be hard, but hard does not mean impossible.",
        "Don't wait for opportunity. Create it.",
        "Sometimes we're tested not to show our weaknesses, but to discover our strengths.",
        "The key to success is to focus on goals, not obstacles.",
        "Dream it. Believe it. Build it.",
        "Work hard in silence, let success make the noise.",
        "Stay positive, work hard, make it happen.",
        "Your business, your rules, your success.",
    ];
    $randomQuote = $quotes[array_rand($quotes)];
@endphp

@if($variant === 'dark')
<!-- Dark Footer for Welcome/Landing Pages -->
<footer class="relative z-10 bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-900">
    <!-- Motivational Quote Section -->
    <div class="border-t border-white/10 py-8">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 mb-4">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                </svg>
            </div>
            <p class="text-xl md:text-2xl font-medium text-white/90 italic mb-3">
                "{{ $randomQuote }}"
            </p>
            <p class="text-indigo-300 text-sm">— Daily inspiration for Shopledger entrepreneurs</p>
        </div>
    </div>

    <!-- Main Footer Content -->
    <div class="border-t border-white/10 py-10 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 50 50" fill="currentColor">
                                <circle cx="12" cy="10" r="4"/>
                                <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
                                <path d="M9 26 L6 38 L8 38 L12 28" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M13 26 L17 38 L15 38 L11 28" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M14 18 L22 20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                                <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22" stroke="currentColor" stroke-width="2" fill="none"/>
                                <circle cx="30" cy="36" r="3"/>
                                <circle cx="40" cy="36" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Shopledger</h3>
                            <p class="text-indigo-200 text-xs">Empowering Rwandan Businesses</p>
                        </div>
                    </div>
                    <p class="text-indigo-200 text-sm leading-relaxed max-w-md">
                        The complete multi-shop management system designed for Rwandan entrepreneurs. 
                        Track sales, manage inventory, and grow your business with confidence.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('login') }}" class="text-indigo-200 hover:text-white transition text-sm">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-indigo-200 hover:text-white transition text-sm">Register</a></li>
                        <li><a href="#features" class="text-indigo-200 hover:text-white transition text-sm">Features</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Get in Touch</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="tel:0786163963" class="flex items-center text-indigo-200 hover:text-white transition text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                0786 163 963
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/250786163963" target="_blank" class="flex items-center text-indigo-200 hover:text-white transition text-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                WhatsApp
                            </a>
                        </li>
                        <li>
                            <span class="flex items-center text-indigo-200 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Kigali, Rwanda
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="mt-10 pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between">
                <p class="text-indigo-200 text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} Shopledger. Made with ❤️ in Rwanda
                </p>
                <div class="flex items-center space-x-4">
                    <span class="text-indigo-300 text-xs flex items-center">
                        <svg class="w-4 h-4 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Secure & Trusted
                    </span>
                    <span class="text-indigo-300 text-xs flex items-center">
                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        4.8/5 Rating
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

@else
<!-- Light Footer for App Dashboard Pages -->
<footer class="bg-white border-t border-gray-200 mt-auto">
    <!-- Motivational Quote Section -->
    <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 py-6">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 mb-3">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                </svg>
            </div>
            <p class="text-lg font-medium text-gray-700 italic">
                "{{ $randomQuote }}"
            </p>
            <p class="text-indigo-500 text-xs mt-2">— Keep pushing forward! 🚀</p>
        </div>
    </div>

    <!-- Main Footer Content -->
    <div class="py-6 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center space-x-3 mb-4 md:mb-0">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 50 50" fill="currentColor">
                        <circle cx="12" cy="10" r="4"/>
                        <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
                        <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22" stroke="currentColor" stroke-width="2" fill="none"/>
                        <circle cx="30" cy="36" r="3"/>
                        <circle cx="40" cy="36" r="3"/>
                    </svg>
                </div>
                <div>
                    <span class="text-gray-800 font-bold">Shopledger</span>
                    <span class="text-gray-400 mx-2">|</span>
                    <span class="text-gray-500 text-sm">Multi-Shop Management</span>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <a href="tel:0786163963" class="text-gray-500 hover:text-indigo-600 transition flex items-center text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Support
                </a>
                <span class="text-gray-300">|</span>
                <p class="text-gray-500 text-sm">
                    © {{ date('Y') }} Made with ❤️ in Rwanda
                </p>
            </div>
        </div>
    </div>
</footer>
@endif
