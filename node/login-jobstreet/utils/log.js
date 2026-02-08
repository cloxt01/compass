/**
 * Safe Logging - Handle broken pipes in background mode
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export const safeLog = (message) => {
    try {
        // Cek apakah stdout masih bisa ditulis
        if (process.stdout.writable) {
            console.log(`[${new Date().toLocaleString()}] ${message}`);
        }
    } catch (e) {
        // Jika pipe rusak, diamkan saja agar tidak crash, 
        // atau tulis ke file log permanen jika perlu
    }
}