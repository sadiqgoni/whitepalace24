import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Restaurant/**/*.php',
        './resources/views/filament/restaurant/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    plugins: [require('tailwindcss-animate')],

}

 