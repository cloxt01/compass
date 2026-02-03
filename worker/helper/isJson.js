function isJson(value) {
  if (typeof value !== 'string') return false;
  try {
    const parsed = JSON.parse(value);
    return typeof parsed === 'object' && parsed !== null;
  } catch {
    return false;
  }
}
export default isJson;