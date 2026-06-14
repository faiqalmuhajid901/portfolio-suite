import { mkdirSync, copyFileSync, existsSync } from 'node:fs';

const candidates = [
  'public/livewire/livewire.min.js',
  'vendor/livewire/livewire/dist/livewire.min.js',
  'vendor/livewire/livewire/dist/livewire.js',
];

const source = candidates.find(existsSync);

if (!source) {
  throw new Error('Livewire JS file not found');
}

mkdirSync('public/build/livewire', { recursive: true });
copyFileSync(source, 'public/build/livewire/livewire.min.js');

console.log(`Livewire JS copied from ${source} to public/build/livewire/livewire.min.js`);