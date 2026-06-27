<template>
    <div>
        <!-- slider area -->
        <HomeSliders />

        <!-- popular categories -->
        <HomePopularCategories />

        <!-- product section 1 -->
        <HomeProductSectionOne />

        <!-- home banner 1 -->
        <HomeBannerSectionOne />

        <!-- shop section 1 -->
        <HomeShopSectionOne v-if="is_addon_activated('multi_vendor')" />

        <!-- home shop banner 1 -->
        <HomeShopBannerSectionOne v-if="is_addon_activated('multi_vendor')" />

        <!-- product section 2 -->
        <HomeProductSectionTwo />

        <!-- home banner 2 -->
        <HomeBannerSectiontwo />

        <!-- shop section 2 -->
        <HomeShopSectionTwo v-if="is_addon_activated('multi_vendor')" />

        <!-- home shop banner 2 -->
        <HomeShopBannerSectionTwo v-if="is_addon_activated('multi_vendor')" />

        <!-- shop section 3 -->
        <HomeShopSectionThree v-if="is_addon_activated('multi_vendor')" />

        <!-- product section 3 -->
        <HomeProductSectionThree />

        <!-- home banner 3 -->
        <HomeBannerSectionThree />
        
        <!-- shop section 4 -->
        <HomeShopSectionFour v-if="is_addon_activated('multi_vendor')" />

        <!-- product section 4 -->
        <HomeProductSectionFour />
        
        <!-- shop section 5 -->
        <HomeShopSectionFive v-if="is_addon_activated('multi_vendor')" />

        <!-- home shop banner 3 -->
        <HomeShopBannerSectionThree  v-if="is_addon_activated('multi_vendor')" />

        <!-- product section 5 -->
        <HomeProductSectionFive />

        <!-- home banner 4 -->
        <HomeBannerSectionFour />

        <!-- product section 6 -->
        <HomeProductSectionSix />

        <div class="py-8 grey lighten-4 mt-8">
            <v-container>
                <HomeAboutText />
                <HomeAllCategories />
            </v-container>
        </div>
    </div>
</template>

<script>
import HomeSliders from "../components/home/HomeSliders";
import HomePopularCategories from "../components/home/HomePopularCategories";

import HomeProductSectionOne from "../components/home/HomeProductSectionOne";
import HomeProductSectionTwo from "../components/home/HomeProductSectionTwo";
import HomeProductSectionThree from "../components/home/HomeProductSectionThree";
import HomeProductSectionFour from "../components/home/HomeProductSectionFour";
import HomeProductSectionFive from "../components/home/HomeProductSectionFive";
import HomeProductSectionSix from "../components/home/HomeProductSectionSix";

import HomeBannerSectionOne from "../components/home/HomeBannerSectionOne";
import HomeBannerSectiontwo from "../components/home/HomeBannerSectiontwo";
import HomeBannerSectionThree from "../components/home/HomeBannerSectionThree";
import HomeBannerSectionFour from "../components/home/HomeBannerSectionFour";

import HomeAboutText from "../components/home/HomeAboutText";
import HomeAllCategories from "../components/home/HomeAllCategories";

import HomeShopSectionOne from '../components/home/HomeShopSectionOne';
import HomeShopBannerSectionOne from '../components/home/HomeShopBannerSectionOne';
import HomeShopSectionTwo from '../components/home/HomeShopSectionTwo';
import HomeShopBannerSectionTwo from '../components/home/HomeShopBannerSectionTwo';
import HomeShopSectionThree from '../components/home/HomeShopSectionThree';
import HomeShopSectionFour from '../components/home/HomeShopSectionFour';
import HomeShopSectionFive from '../components/home/HomeShopSectionFive';
import HomeShopBannerSectionThree from '../components/home/HomeShopBannerSectionThree';

export default {
    components: {
        HomeSliders,
        HomePopularCategories,
        HomeProductSectionOne,
        HomeProductSectionTwo,
        HomeProductSectionThree,
        HomeProductSectionFour,
        HomeProductSectionFive,
        HomeProductSectionSix,
        HomeBannerSectionOne,
        HomeBannerSectiontwo,
        HomeBannerSectionThree,
        HomeBannerSectionFour,
        HomeAboutText,
        HomeAllCategories,
        HomeShopSectionOne,
        HomeShopBannerSectionOne,
        HomeShopSectionTwo,
        HomeShopBannerSectionTwo,
        HomeShopSectionThree,
        HomeShopSectionFour,
        HomeShopSectionFive,
        HomeShopBannerSectionThree,
    },
};
</script>
