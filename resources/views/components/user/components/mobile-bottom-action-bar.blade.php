@props(['class' => ''])

@once
    <style>
        @media (max-width: 768px) {
            .desktop-only-action {
                display: none !important;
            }

            .mobile-action-safe-space {
                padding-bottom: 8rem !important;
            }
        }

        @media (min-width: 769px) {
            .mobile-bottom-action-bar {
                display: none !important;
            }
        }
    </style>
@endonce

<div {{ $attributes->merge([
    'class' => 'mobile-bottom-action-bar fixed inset-x-0 bottom-0 z-[220] block bg-white px-4 pt-3 shadow-[0_-12px_32px_rgba(95,74,58,0.14)] ' . $class,
    'style' => 'padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));',
]) }}>
    {{ $slot }}
</div>
