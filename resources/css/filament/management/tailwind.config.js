import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Management/**/*.php',
        './resources/views/filament/management/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}

 