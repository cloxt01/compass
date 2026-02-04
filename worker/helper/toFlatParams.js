function toFlatParams (payload, otp) {
    const { authParams, ...rest } = JSON.parse(payload);

    const flatParams = {
    ...rest,
    ...authParams,
    protocol: 'oauth2',
    verification_code: otp,
    auth0Client: process.env.AUTH0CLIENT
    };
    return flatParams;
}

export default toFlatParams;