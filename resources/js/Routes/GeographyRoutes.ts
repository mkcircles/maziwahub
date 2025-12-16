import { RouteRecordRaw } from 'vue-router';



const geographyRoutes: RouteRecordRaw[] = [
    {
        path: '/countries',
        name: 'countries',
        component: () => import('../views/geography/CountriesView.vue'),
    },
    {
        path: '/countries/:id',
        name: 'country-detail',
        component: () => import('../views/geography/CountryDetailView.vue'),
    },
    {
        path: '/regions',
        name: 'regions',
        component: () => import('../views/geography/RegionsView.vue'),
    },
    {
        path: '/regions/:id',
        name: 'region-detail',
        component: () => import('../views/geography/RegionDetailView.vue'),
    },
    {
        path: '/districts',
        name: 'districts',
        component: () => import('../views/geography/DistrictsView.vue'),
    },
    {
        path: '/districts/:id',
        name: 'district-detail',
        component: () => import('../views/geography/DistrictDetailView.vue'),
    },
    {
        path: '/counties',
        name: 'counties',
        component: () => import('../views/geography/CountiesView.vue'),
    },
    {
        path: '/counties/:id',
        name: 'county-detail',
        component: () => import('../views/geography/CountyDetailView.vue'),
    },
    {
        path: '/subcounties',
        name: 'subcounties',
        component: () => import('../views/geography/SubcountiesView.vue'),
    },
    {
        path: '/subcounties/:id',
        name: 'subcounty-detail',
        component: () => import('../views/geography/SubcountyDetailView.vue'),
    },
    {
        path: '/parishes',
        name: 'parishes',
        component: () => import('../views/geography/ParishesView.vue'),
    },
    {
        path: '/parishes/:id',
        name: 'parish-detail',
        component: () => import('../views/geography/ParishDetailView.vue'),
    },
    {
        path: '/villages',
        name: 'villages',
        component: () => import('../views/geography/VillagesView.vue'),
    },
    {
        path: '/villages/:id',
        name: 'village-detail',
        component: () => import('../views/geography/VillageDetailView.vue'),
    },
];

export default geographyRoutes;

