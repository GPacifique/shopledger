<svg viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Person pushing cart icon -->
    <g fill="currentColor">
        <!-- Person body -->
        <circle cx="12" cy="10" r="4"/>
        <path d="M8 16 L10 26 L14 26 L16 16 Z"/>
        <!-- Person legs walking -->
        <path d="M9 26 L6 38 L8 38 L12 28" stroke="currentColor" stroke-width="2" fill="none"/>
        <path d="M13 26 L17 38 L15 38 L11 28" stroke="currentColor" stroke-width="2" fill="none"/>
        <!-- Person arms pushing -->
        <path d="M14 18 L22 20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>

        <!-- Shopping Cart -->
        <path d="M22 18 L24 18 L28 32 L42 32 L45 22 L26 22" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>
        <!-- Cart wheels -->
        <circle cx="30" cy="36" r="3"/>
        <circle cx="40" cy="36" r="3"/>
        <!-- Items in cart -->
        <rect x="28" y="24" width="4" height="6" rx="1" fill="currentColor" opacity="0.6"/>
        <rect x="34" y="25" width="5" height="5" rx="1" fill="currentColor" opacity="0.6"/>
    </g>

    <!-- Shopledger Text -->
    <text x="52" y="32" font-family="system-ui, -apple-system, sans-serif" font-size="18" font-weight="700" fill="currentColor">
        Shop<tspan fill="currentColor" opacity="0.7">ledger</tspan>
    </text>
</svg>
