// Lightweight resilient logger with color

const pad = (n) => String(n).padStart(2, '0');

const nowPlus7 = () => {
  const d = new Date(Date.now() + 7 * 60 * 60 * 1000);
  return (
    d.getUTCFullYear() + '-' +
    pad(d.getUTCMonth() + 1) + '-' +
    pad(d.getUTCDate()) + ' ' +
    pad(d.getUTCHours()) + ':' +
    pad(d.getUTCMinutes()) + ':' +
    pad(d.getUTCSeconds())
  );
};

// ANSI colors
const C = {
  reset: '\x1b[0m',
  gray: '\x1b[90m',
  red: '\x1b[31m',
  green: '\x1b[32m',
  yellow: '\x1b[33m',
  blue: '\x1b[34m'
};

const levelColor = {
  info: C.green,
  warn: C.yellow,
  error: C.red,
  debug: C.blue
};

const fmt = (level, args) =>
  `${C.gray}${nowPlus7()}${C.reset} ` +
  `${levelColor[level]}[${level}]${C.reset}: ` +
  args.map(a => {
    if (typeof a === 'string') return a;
    try { return JSON.stringify(a); }
    catch { return String(a); }
  }).join(' ');

const base = {
  info: (...args) => console.log(fmt('info', args)),
  warn: (...args) => console.warn(fmt('warn', args)),
  error: (...args) => console.error(fmt('error', args)),
  debug: (...args) => console.debug(fmt('debug', args)),
  child: (meta) => ({
    info: (...args) => base.info(meta, ...args),
    warn: (...args) => base.warn(meta, ...args),
    error: (...args) => base.error(meta, ...args),
    debug: (...args) => base.debug(meta, ...args),
    child: () => base
  })
};

export default base;
