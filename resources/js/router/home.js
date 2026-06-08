let Home = () => import("../pages/Home");
let ProductDetails = () => import("../pages/ProductDetails");
let ServiceDetails = () => import("../pages/ServiceDetails");
let BrandDetails = () => import("../pages/BrandDetails");
let ProductListing = () => import("../pages/ProductListing");
let TrackOrder = () => import("../pages/TrackOrder");
let AllCategories = () => import("../pages/AllCategories");
let AllBrands = () => import("../pages/AllBrands");
let AllOffers = () => import("../pages/AllOffers");
let OfferDetails = () => import("../pages/OfferDetails");
let AllBlogs = () => import("../pages/AllBlogs");
let BlogDetails = () => import("../pages/BlogDetails");
let TestComponents = () => import("../pages/TestComponents");
let Home2 = () => import("../pages/Home2");
let Shop = () => import("../pages/Shop");
let About = () => import("../pages/About");
let Collection = () => import("../pages/Collection");

let ShopCartoneria = () => import("../pages/shop/ShopCartoneria");
let ShopAseo = () => import("../pages/shop/ShopAseo");
let ShopPapeleria = () => import("../pages/shop/ShopPapeleria");
let ShopCafeteria = () => import("../pages/shop/ShopCafeteria");
let ShopShowApi = () => import("../pages/shop/ShopShowApi.vue");
let ShopSeguridadIndustrial = ()=> import("../pages/shop/ShopSeguridadIndustrial.vue");
let ShopTecnologia = ()=> import("../pages/shop/ShopTecnologia.vue");


export default [
    {
        path: "/test-components",
        component: TestComponents,
        name: "TestComponents",
        meta: { requiresAuth: false }
    },
    {
        path: "/about",
        component: About,
        name: "About",
        meta: { requiresAuth: false }
    },
    {
        path: "/about/brand/:slug?",
        component: About,
        name: "AboutBrand",
        meta: { requiresAuth: false }
    },
    {
        path: "/brands",
        component: AllBrands,
        name: "Brands",
        meta: { requiresAuth: false }
    },
    {
        path: "/shop",
        component: Shop,
        name: "Shop",
        meta: { requiresAuth: false },
        children: [
            {
                path: "cartoneria",
                component: ShopCartoneria,
                name: "ShopCartoneria",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: "aseo",
                component: ShopAseo,
                name: "ShopAseo",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: "papeleria",
                component: ShopPapeleria,
                name: "ShopPapeleria",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: "cafeteria",
                component: ShopCafeteria,
                name: "ShopCafeteria",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: "ShopShowApi",
                component: ShopShowApi,
                name: "ShopShowApi",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: "tecnologia",
                component: ShopTecnologia,
                name: "ShopTecnologia",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: "seguridad-industrial",
                component: ShopSeguridadIndustrial,
                name: "ShopSeguridadIndustrial",
                meta: { requiresAuth: false, hasBottomBar: false }
            },
            {
                path: ":categorySlug",
                component: ShopShowApi,
                name: "ShopDynamicCategory",
                props: route => ({
                    category: route.query.category || route.params.categorySlug
                }),
                meta: { requiresAuth: false, hasBottomBar: false }
            }
        ]
    },
    {
        path: "/home",
        component: Home,
        name: "Home",
        meta: { requiresAuth: false }
    },
    {
        path: "/",
        component: Home2,
        name: "Home2",
        meta: { requiresAuth: false }
    },
    {
        path: "/all-categories",
        component: AllCategories,
        name: "AllCategories",
        meta: { requiresAuth: false }
    },
    {
        path: "/all-brands",
        component: AllBrands,
        name: "AllBrands",
        meta: { requiresAuth: false }
    },
    {
        path: "/all-offers",
        component: AllOffers,
        name: "AllOffers",
        meta: { requiresAuth: false }
    },
    {
        path: "/offer/:offerSlug?",
        component: OfferDetails,
        name: "OfferDetails",
        meta: { requiresAuth: false }
    },
    // {
    //     path: "/shop",
    //     component: ProductListing,
    //     name: "Shop",
    //     meta: { requiresAuth: false }
    // },
    {
        path: "/category/:categorySlug?",
        component: ProductListing,
        name: "Category",
        meta: { requiresAuth: false }
    },
    {
        path: "/brand/:brandId?",
        component: ProductListing,
        name: "Brand",
        meta: { requiresAuth: false }
    },
    {
        path: "/category2/:categorySlug?",
        component: ProductListing,
        name: "Category2",
        meta: { requiresAuth: false }
    },
    {
        path: "/search/:keyword?",
        component: ProductListing,
        name: "Search",
        meta: { requiresAuth: false, shareble: true }
    },
    {
        path: "/product/:slug",
        component: ProductDetails,
        name: "ProductDetails",
        meta: { requiresAuth: false, shareble: true }
    },
    {
        path: "/service/:slug",
        component: ServiceDetails,
        name: "ServiceDetails",
        meta: { requiresAuth: false }
    },
    {
        path: "/brand/:slug?",
        component: BrandDetails,
        name: "BrandDetails",
        meta: { requiresAuth: false }
    },
    {
        path: "/track-order",
        component: TrackOrder,
        name: "TrackOrder",
        meta: { requiresAuth: true }
    },
    {
        path: "/all-blogs",
        component: AllBlogs,
        name: "AllBlogs",
        meta: { requiresAuth: false }
    },
    {
        path: "/all-blogs/category/:categorySlug?",
        component: AllBlogs,
        name: "AllBlogsFilter",
        meta: { requiresAuth: false }
    },
    {
        path: "/all-blogs/search/:searchKeyword?",
        component: AllBlogs,
        name: "SearchBlogs",
        meta: { requiresAuth: false }
    },
    {
        path: "/blog-details/:slug",
        component: BlogDetails,
        name: "BlogDetails",
        meta: { requiresAuth: false }
    },
    {
        path: "/collection/:slug",
        component: Collection,
        name: "Collection",
        meta: { requiresAuth: false }
    },
    {
        path: "/plan/:slug",
        component: Collection,
        name: "PlanDetails",
        meta: { requiresAuth: false }
    }
];
