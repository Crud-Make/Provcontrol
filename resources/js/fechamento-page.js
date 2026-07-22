import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import fechamentoForm from './fechamento';

window.Chart = Chart;

Alpine.data('fechamentoForm', fechamentoForm);
Alpine.start();
