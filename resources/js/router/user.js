let Cart = () => import("../pages/user/Cart");
let Invoices = () => import("../pages/user/Invoices");
let InvoicesDetails = () => import("../pages/user/InvoicesDetails");
let DashBoard = () => import("../pages/user/Dashboard");
let PurchaseHistory = () => import("../pages/user/PurchaseHistory");
let Proyects = () => import("../pages/user/Proyects");
let Suscriptions = () => import("../pages/user/Suscriptions");
let Downloads = () => import("../pages/user/Downloads");
let OrderDetails = () => import("../pages/user/OrderDetails");
let ProyectDetails = () => import("../pages/user/ProyectDetails");
let SuscriptionDetails = () => import("../pages/user/SuscriptionDetails");
let RefundRequests = () => import("../pages/user/refund_request/RefundRequestList");
let RefundRequestCreate = () => import("../pages/user/refund_request/RefundRequestCreate");
let Wishlist = () => import("../pages/user/Wishlist");
let Conversations = () => import("../pages/user/Conversations");
let ConversationDetails = () => import("../pages/user/ConversationDetails");
let FollowedShops = () => import("../pages/user/FollowedShops");
let Wallet = () => import("../pages/user/Wallet");
let EarningPoints = () => import("../pages/user/EarningPoints");
let Coupon = () => import("../pages/user/Coupon");
let Profile = () => import("../pages/user/Profile");
let Profile2 = () => import("../pages/user/ProfileOld");
let UserLayout = () => import("../components/user/UserLayout");
let NotificationAll = () => import("../pages/user/Notification1");
let Notification = () => import("../pages/user/Notification2");
let Error404 = () => import("../pages/errors/404");

export default [
    {
        path: "/user/",
        component: UserLayout,
        redirect: "/user/dashboard",
        children: [
            {
                path: "checkout",
                component: Cart,
                name: "Cart",
                meta: { requiresAuth: true }
            },
            {
                path: "invoices",
                component: Invoices,
                name: "Invoices",
                meta: { requiresAuth: true }
            },
            {
                path: "invoicesDetails",
                component: InvoicesDetails,
                name: "InvoicesDetails",
                meta: { requiresAuth: true }
            },
            {
                path: "dashboard",
                component: DashBoard,
                name: "DashBoard",
                meta: { requiresAuth: true }
            },
            {
                path: "purchase-history",
                component: PurchaseHistory,
                name: "PurchaseHistory",
                meta: { requiresAuth: true }
            },
            {
                path: "proyects",
                component: Proyects,
                name: "Proyects",
                meta: { requiresAuth: true }
            },
            {
                path: "proyects/:code",
                component: ProyectDetails,
                name: "ProyectDetails",
                meta: { requiresAuth: true }
            },
            {
                path: "suscriptions",
                component: Suscriptions,
                name: "Suscriptions",
                meta: { requiresAuth: true }
            },
            {
                path: "suscriptions/:code",
                component: SuscriptionDetails,
                name: "SuscriptionDetails",
                meta: { requiresAuth: true }
            },
            {
                path: "downloads",
                component: Downloads,
                name: "Downloads",
                meta: { requiresAuth: true }
            },
            {
                path: "purchase-history/:code",
                component: OrderDetails,
                name: "OrderDetails",
                meta: { requiresAuth: true }
            },
            {
                path: "refund-requests",
                component: RefundRequests,
                name: "RefundRequests",
                meta: { requiresAuth: true }
            },
            {
                path: "create-refund-request/:orderId",
                component: RefundRequestCreate,
                name: "CreateRefundRequest",
                meta: { requiresAuth: true }
            },
            {
                path: "wishlist",
                component: Wishlist,
                name: "Wishlist",
                meta: { requiresAuth: true }
            },
            {
                path: "followed-shops",
                component: FollowedShops,
                name: "FollowedShops",
                meta: { requiresAuth: true }
            },

            {
                path: "product-query",
                component: window.shopSetting.general_settings.conversation_system == 1 ? Conversations : Error404,
                name: "Conversations",
                meta: { requiresAuth: true }
            },
            {
                path: "product-query/:slug",
                component:
                    window.shopSetting.general_settings.conversation_system == 1 ? ConversationDetails : Error404,
                name: "ConversationsDetails",
                meta: { requiresAuth: true }
            },
            {
                path: "wallet",
                component: window.shopSetting.general_settings.wallet_system == 1 ? Wallet : Error404,
                name: "Wallet",
                meta: { requiresAuth: true }
            },

            {
                path: "earning-points",
                component: window.shopSetting.general_settings.club_point == 1 ? EarningPoints : Error404,
                name: "Earning",
                meta: { requiresAuth: true }
            },

            {
                path: "coupon",
                component: Coupon,
                name: "Coupon",
                meta: { requiresAuth: true }
            },
            {
                path: "profile",
                component: Profile,
                name: "Profile",
                meta: { requiresAuth: true }
            },
            {
                path: "profile2",
                component: Profile2,
                name: "Profile2",
                meta: { requiresAuth: true }
            },
            {
                path: "notifications",
                component: NotificationAll,
                name: "NotificationAll",
                meta: { requiresAuth: true }
            },
            {
                path: "notification",
                component: Notification,
                name: "Notification",
                meta: { requiresAuth: true }
            }
        ]
    }
];
