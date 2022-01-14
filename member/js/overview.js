/*
Packery JavaScript Library 
https://packery.metafizzy.co/

Draggabilly
https://draggabilly.desandro.com/
*/

let elem = document.querySelector('.grid');
let pckry = new Packery(elem, {
    itemSelector: '.grid-item',
    gutter: 10,
    columnWidth: '.grid-item'
});

pckry.getItemElements().forEach( function( itemElem ) {
    var draggie = new Draggabilly( itemElem );
    pckry.bindDraggabillyEvents( draggie );
});
  