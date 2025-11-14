import { RouteRecordRaw } from 'vue-router';

import CountriesView from '../views/geography/CountriesView.vue';
import CountryDetailView from '../views/geography/CountryDetailView.vue';
import RegionsView from '../views/geography/RegionsView.vue';
import RegionDetailView from '../views/geography/RegionDetailView.vue';
import DistrictsView from '../views/geography/DistrictsView.vue';
import DistrictDetailView from '../views/geography/DistrictDetailView.vue';
import CountiesView from '../views/geography/CountiesView.vue';
import CountyDetailView from '../views/geography/CountyDetailView.vue';
import SubcountiesView from '../views/geography/SubcountiesView.vue';
import SubcountyDetailView from '../views/geography/SubcountyDetailView.vue';
import ParishesView from '../views/geography/ParishesView.vue';
import ParishDetailView from '../views/geography/ParishDetailView.vue';
import VillagesView from '../views/geography/VillagesView.vue';
import VillageDetailView from '../views/geography/VillageDetailView.vue';

const geographyRoutes: RouteRecordRaw[] = [
    {
        path: '/countries',
        name: 'countries',
        component: () => CountriesView,
    },
    {
        path: '/countries/:id',
        name: 'country-detail',
        component: () => CountryDetailView,
    },
    {
        path: '/regions',
        name: 'regions',
        component: () => RegionsView,
    },
    {
        path: '/regions/:id',
        name: 'region-detail',
        component: () => RegionDetailView,
    },
    {
        path: '/districts',
        name: 'districts',
        component: () => DistrictsView,
    },
    {
        path: '/districts/:id',
        name: 'district-detail',
        component: () => DistrictDetailView,
    },
    {
        path: '/counties',
        name: 'counties',
        component: () => CountiesView,
    },
    {
        path: '/counties/:id',
        name: 'county-detail',
        component: () => CountyDetailView,
    },
    {
        path: '/subcounties',
        name: 'subcounties',
        component: () => SubcountiesView,
    },
    {
        path: '/subcounties/:id',
        name: 'subcounty-detail',
        component: () => SubcountyDetailView,
    },
    {
        path: '/parishes',
        name: 'parishes',
        component: () => ParishesView,
    },
    {
        path: '/parishes/:id',
        name: 'parish-detail',
        component: () => ParishDetailView,
    },
    {
        path: '/villages',
        name: 'villages',
        component: () => VillagesView,
    },
    {
        path: '/villages/:id',
        name: 'village-detail',
        component: () => VillageDetailView,
    },
];

export default geographyRoutes;

