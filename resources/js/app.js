import './bootstrap';

import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import SignaturePad from 'signature_pad';

window.Alpine = Alpine;
window.SignaturePad = SignaturePad;

Alpine.plugin(mask);
Alpine.start();
