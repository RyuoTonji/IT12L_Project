import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

// Import dashboard charts functions
import { initializeDashboardCharts, startLiveRefresh } from './dashboard-charts.js';

window.Alpine = Alpine;
Alpine.start();

// Make ApexCharts globally available BEFORE setting the flag
window.ApexCharts = ApexCharts;

// Make dashboard functions globally available BEFORE setting the flag
window.initializeDashboardCharts = initializeDa

shboardCharts;
window.startLiveRefresh = startLiveRefresh;

// Log immediately to verify
console.log('✅ app.js loaded');
console.log('✅ ApexCharts available:', typeof window.ApexCharts !== 'undefined');
console.log('✅ initializeDashboardCharts available:', typeof window.initializeDashboardCharts !== 'undefined');
console.log('✅ startLiveRefresh available:', typeof window.startLiveRefresh !== 'undefined');

// Flag to indicate app.js has loaded - SET THIS LAST
window.appJsLoaded = true;