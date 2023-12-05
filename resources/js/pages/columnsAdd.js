import { ObjectEprpoDatas } from './Reconciliation.vue';

const columnsAdd = [
// { data: 'action', title: 'Action'},
{
data: 'action',
title: 'Action',
orderable: false,
searchable: false,
createdCell(cell) {
// * Button View
cell.querySelector('input[type="checkbox"]').addEventListener('click', function () {
let eprpoData = this.getAttribute('data-eprpo');
ObjectEprpoDatas.value = eprpoData;
// console.log(eprpoData);
});
},
},
{ data: 'reference_po_number', title: 'PO Number' },
{ data: 'po_number', title: 'PR Number' },
{ data: 'other_reference', title: 'Invoice Number' },
{ data: 'item_code', title: 'Code' },
{ data: 'item_name', title: 'Name' },
{ data: 'description', title: 'Description' },
{ data: 'supplier_name', title: 'Supplier' },
{ data: 'classification_code', title: 'Classification' },
];
