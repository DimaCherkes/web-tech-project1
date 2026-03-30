const API_PATHS = {
    countries: '/project1/api/allCountries',
    disciplines: '/project1/api/allDisciplines',
    games: '/project1/api/allOlympicGames',
    medals: '/project1/api/allAthleteMedals',
    athletes: '/project1/api/allAthletes'
};

const CRUD_PATHS = {
    countries: '/project1/api/countries/',
    disciplines: '/project1/api/discipline/',
    games: '/project1/api/olympicGame/',
    medals: '/project1/api/athleteMedal/',
    athletes: '/project1/api/athletes/',
    createCountry: '/project1/api/createCountry',
    createDiscipline: '/project1/api/createDiscipline',
    createGame: '/project1/api/createOlympicGame',
    createMedal: '/project1/api/createAthleteMedal',
    createAthlete: '/project1/api/createAthlete'
};

document.addEventListener('DOMContentLoaded', () => {
    refreshData('countries');
    setupFormListeners();
});

function showLoading(show) {
    document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
}

async function refreshData(tabId) {
    showLoading(true);
    try {
        const response = await fetch(`${API_PATHS[tabId]}?pageSize=1000`);
        const result = await response.json();
        
        // Fix: Support both "items" and "data" keys due to inconsistent API
        const items = result.items || result.data || [];
        console.log(`Loaded ${tabId}:`, items);
        
        const tableBody = document.querySelector(`#${tabId}Table tbody`);
        if (tableBody) {
            tableBody.innerHTML = '';
            items.forEach(item => {
                const row = createTableRow(tabId, item);
                tableBody.appendChild(row);
            });
        }

        // Update dropdowns in forms
        if (tabId === 'countries') updateDropdown('gameCountrySelect', items);
        if (tabId === 'athletes') updateDropdown('medalAthleteSelect', items, a => `${a.firstName} ${a.lastName}`);
        if (tabId === 'games') updateDropdown('medalGameSelect', items, g => `${g.year} ${g.type} (${g.city})`);
        if (tabId === 'disciplines') updateDropdown('medalDisciplineSelect', items, d => `${d.name} (${d.category || '-'})`);

        // If medals tab is active, we need to ensure all dropdowns are loaded
        if (tabId === 'medals') {
            loadDropdownsForMedals();
        }

    } catch (error) {
        console.error(`Error refreshing ${tabId}:`, error);
    } finally {
        showLoading(false);
    }
}

async function loadDropdownsForMedals() {
    // Only fetch if they aren't populated
    const athleteSelect = document.getElementById('medalAthleteSelect');
    if (athleteSelect && athleteSelect.children.length <= 1) refreshData('athletes');
    
    const gameSelect = document.getElementById('medalGameSelect');
    if (gameSelect && gameSelect.children.length <= 1) refreshData('games');
    
    const disciplineSelect = document.getElementById('medalDisciplineSelect');
    if (disciplineSelect && disciplineSelect.children.length <= 1) refreshData('disciplines');
}

function updateDropdown(id, items, textFn = item => item.name) {
    const select = document.getElementById(id);
    if (!select) return;
    const currentValue = select.value;
    select.innerHTML = '<option value="">-- Vyberte --</option>';
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = textFn(item);
        select.appendChild(opt);
    });
    if (currentValue) select.value = currentValue;
}

function createTableRow(tabId, item) {
    const tr = document.createElement('tr');
    
    if (tabId === 'countries') {
        tr.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.code || '-'}</td>`;
    } else if (tabId === 'disciplines') {
        tr.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.category || '-'}</td>`;
    } else if (tabId === 'games') {
        tr.innerHTML = `<td>${item.year}</td><td>${item.type}</td><td>${item.city}</td>`;
    } else if (tabId === 'medals') {
        // Handle database keys (snake_case)
        const name = (item.first_name && item.last_name) ? `${item.first_name} ${item.last_name}` : `${item.firstName} ${item.lastName}`;
        tr.innerHTML = `<td>${name}</td><td>${item.year} (${item.medal_name || item.medalName || '-'})</td><td>${item.discipline_name || item.disciplineName || '-'}</td><td>${item.placing || '-'}</td>`;
    } else if (tabId === 'athletes') {
        // AthleteDTO uses camelCase
        tr.innerHTML = `<td>${item.id}</td><td>${item.firstName}</td><td>${item.lastName}</td><td>${item.birthDate || '-'}</td>`;
    }

    const actionsTd = document.createElement('td');
    actionsTd.className = 'action-btns';
    
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Upraviť';
    editBtn.className = 'btn-sm btn-warning';
    editBtn.onclick = () => openEditModal(tabId, item);
    
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Zmazať';
    deleteBtn.className = 'btn-sm btn-danger';
    deleteBtn.onclick = () => deleteItem(tabId, item.id);
    
    actionsTd.appendChild(editBtn);
    actionsTd.appendChild(deleteBtn);
    tr.appendChild(actionsTd);
    
    return tr;
}

function setupFormListeners() {
    const forms = {
        addCountryForm: { url: CRUD_PATHS.createCountry, tab: 'countries' },
        addDisciplineForm: { url: CRUD_PATHS.createDiscipline, tab: 'disciplines' },
        addGameForm: { url: CRUD_PATHS.createGame, tab: 'games' },
        addMedalForm: { url: CRUD_PATHS.createMedal, tab: 'medals' }
    };

    Object.keys(forms).forEach(id => {
        const formEl = document.getElementById(id);
        if (!formEl) return;
        formEl.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(formEl);
            const data = Object.fromEntries(formData.entries());
            
            // Map fields for Games to match Service expectations
            if (id === 'addGameForm') {
                data.countryId = parseInt(data.country_id);
                delete data.country_id;
            }

            // Map fields for Medals to match Service expectations
            if (id === 'addMedalForm') {
                data.athleteId = parseInt(data.athlete_id);
                data.gameId = parseInt(data.olympic_games_id);
                data.disciplineId = parseInt(data.discipline_id);
                data.medalTypeId = parseInt(data.placing); // Placed in placing input but should be ID
            }

            try {
                const res = await fetch(forms[id].url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    alert('Záznam pridaný.');
                    formEl.reset();
                    refreshData(forms[id].tab);
                } else {
                    const err = await res.json();
                    alert('Chyba: ' + (err.error || 'Neznáma chyba'));
                }
            } catch (error) {
                console.error('Submit error:', error);
                alert('Chyba pripojenia.');
            }
        });
    });
}

async function deleteItem(tabId, id) {
    if (!confirm('Naozaj chcete zmazať tento záznam?')) return;
    
    try {
        const res = await fetch(`${CRUD_PATHS[tabId]}${id}`, {
            method: 'DELETE'
        });
        if (res.status === 204 || res.ok) {
            alert('Zmazané.');
            refreshData(tabId);
        } else {
            const err = await res.json();
            alert('Chyba pri mazaní: ' + (err.error || 'Neznáma chyba'));
        }
    } catch (error) {
        alert('Chyba pripojenia.');
    }
}

let currentEditTab = '';
let currentEditId = null;

async function openEditModal(tabId, item) {
    currentEditTab = tabId;
    currentEditId = item.id;

    document.getElementById('modalTitle').textContent = `Upraviť ${tabId}`;
    const fieldsContainer = document.getElementById('editFields');
    fieldsContainer.innerHTML = '';
    
    if (tabId === 'countries') {
        fieldsContainer.appendChild(createField('name', 'Názov', item.name));
        fieldsContainer.appendChild(createField('code', 'Kód', item.code));
    } else if (tabId === 'disciplines') {
        fieldsContainer.appendChild(createField('name', 'Názov', item.name));
        fieldsContainer.appendChild(createField('category', 'Kategória', item.category));
    } else if (tabId === 'games') {
        fieldsContainer.appendChild(createField('year', 'Rok', item.year, 'number'));
        fieldsContainer.appendChild(createField('type', 'Typ (LOH/ZOH)', item.type));
        fieldsContainer.appendChild(createField('city', 'Mesto', item.city));
    } else if (tabId === 'athletes') {
        fieldsContainer.appendChild(createField('firstName', 'Meno', item.firstName));
        fieldsContainer.appendChild(createField('lastName', 'Priezvisko', item.lastName));
        fieldsContainer.appendChild(createField('birthDate', 'Dátum narodenia', item.birthDate, 'date'));
    }

    document.getElementById('editModal').style.display = 'block';
}

function createField(name, label, value, type = 'text') {
    const div = document.createElement('div');
    div.className = 'form-group';
    div.innerHTML = `<label>${label}:</label><input type="${type}" name="${name}" value="${value || ''}" class="form-group input" style="width:100%">`;
    return div;
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    if (data.year) data.year = parseInt(data.year);

    try {
        const res = await fetch(`${CRUD_PATHS[currentEditTab]}${currentEditId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (res.ok) {
            alert('Aktualizované.');
            closeModal();
            refreshData(currentEditTab);
        } else {
            const err = await res.json();
            alert('Chyba pri aktualizácii: ' + (err.error || 'Neznáма ошибка'));
        }
    } catch (error) {
        alert('Chyba pripojenia.');
    }
});

window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeModal();
    }
}
