import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Frontdesk/**/*.php',
        './resources/views/filament/frontdesk/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}

 