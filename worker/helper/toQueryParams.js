function toQueryParams(obj, prefix) {
    const str = [];
    for (let p in obj) {
        if (!obj.hasOwnProperty(p)) continue;
        const k = prefix ? `${prefix}[${p}]` : p;
        const v = obj[p];
        if (v !== null && typeof v === "object") {
            str.push(toQueryParams(v, k));
        } else {
            str.push(`${encodeURIComponent(k)}=${encodeURIComponent(v)}`);
        }
    }
    return str.join("&");
}

export default toQueryParams;