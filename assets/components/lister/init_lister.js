const grids = []
const maxGrids = 120

for(let i=0; maxGrids>i; i++) {
    grids[i] = document.getElementById('gridDemo' + i)
    if(grids[i]) {
        new Sortable(grids[i], {
            animation: 150,
            group: 'shared', // set both lists to same group
            ghostClass: 'blue-background-class'
        });
    }
}