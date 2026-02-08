// Lightweight resilient logger used by login-optimized and adapters
const fmt = (level, args) => `${new Date().toISOString()} [${level}]: ${args.map(a => {
  if (typeof a === 'string') return a;
  try { return JSON.stringify(a); } catch (e) { return String(a); }
}).join(' ')}`;

const base = {
  info: (...args) => console.log(fmt('info', args)),
  warn: (...args) => console.warn(fmt('warn', args)),
  error: (...args) => console.error(fmt('error', args)),
  debug: (...args) => console.debug(fmt('debug', args)),
  child: (meta) => {
    return {
      info: (...args) => base.info(meta, ...args),
      warn: (...args) => base.warn(meta, ...args),
      error: (...args) => base.error(meta, ...args),
      debug: (...args) => base.debug(meta, ...args),
      child: () => base
    };
  }
};

export default base;
