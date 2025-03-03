export function IsApiResponse(obj) {
    if (obj && typeof obj === 'object') {
        return ['is_success', 'is_error', 'data'].reduce(
            (is, field) => is * (field in obj),
            true
        );
    }
    return false;
}

export function ToApiResponse(data) {
    return {
        is_success: false,
        is_error: false,
        data: data
    };
}