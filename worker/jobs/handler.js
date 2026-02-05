async function handler(id, operation , data) {
    let { default: handler } = await import(`./${operation}-${data.provider}.js`);
    let result = await handler(id, data);
    return result;
}

export default handler;