export default function init(store, router) {
    if (
        !localStorage.getItem("shopCacheVersion") ||
        localStorage.getItem("shopCacheVersion") != shopSetting.cacheVersion
    ) {
        deleteCaches().then(() => {
            localStorage.setItem("shopCacheVersion", shopSetting.cacheVersion);
        });
    }

    // Response interceptor
    axios.interceptors.response.use(
        response => {
            return response;
        },
        function (error) {
            if (error.response.status == 401) {
                store.dispatch("auth/logout");
                router.push({ name: "Home2" });
            }

            return Promise.reject(error);
        }
    );

    // Request interceptor
    axios.interceptors.request.use(request => {
        // if has token send token
        const token = store.getters["auth/accessToken"];
        if (token) {
            request.headers.common.Authorization = `Bearer ${token}`;
        }

        // send locale
        request.headers.common["Accept-Language"] = store.getters["app/userLanguage"];

        return request;
    });
}

async function deleteCaches() {
    try {
        const keys = await window.caches.keys();
        await Promise.all(keys.map(key => caches.delete(key)));

        // console.log("deleteCache succeed");
    } catch (err) {
        // console.log("deleteCache err: ", err);
    }
}
