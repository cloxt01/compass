/**
 * File Operations
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { safeLog } from './log.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export function saveToFile(filename, data) {
    try {
        // Path ke login-automation folder
        const filePath = path.join(__dirname, '../', filename);
        fs.writeFileSync(filePath, JSON.stringify(data, null, 2));
        safeLog(`  ✅ Saved: ${filename}`);
        return true;
    } catch (err) {
        safeLog(`  ❌ Error saving ${filename}: ${err.message}`);
        return false;
    }
}
