import { mkdirSync, copyFileSync, existsSync } from 'node:fs';

const candidates = [
  'vendor/livewire/livewire/dist/livewire.min.js',
  'vendor/livewire/livewire/dist/livewire.js',
];

const source = candidates.find(existsSync);

if (!source) {
  throw new Error('Livewire JS file not found in vendor/livewire/livewire/dist');
}

mkdirSync('public/build/livewire', { recursive: true });
copyFileSync(source, 'public/build/livewire/livewire.min.js');

console.log('Livewire JS copied to public/build/livewire/livewire.min.js');