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

// Global state
let allCountries = [];
let allAthletes = [];
let allGames = [];
let allDisciplines = [];
let medalTypesMap = {};

const tabTitles = {
    countries: 'krajinu',
    disciplines: 'disciplínu',
    games: 'hry',
    medals: 'medailu',
    athletes: 'športovca'
};

const medalTranslations = {
    'Gold': 'Zlato',
    'Silver': 'Striebro',
    'Bronze': 'Bronz'
};

document.addEventListener('DOMContentLoaded', () => {
    refreshData('countries');
    setupFormListeners();
});

function showNotification(message, type = 'success') {
    const area = document.getElementById('notificationArea');
    if (!area) return;

    const note = document.createElement('div');
    note.className = `notification ${type}`;
    note.textContent = message;

    area.appendChild(note);

    setTimeout(() => {
        note.style.opacity = '0';
        note.style.transform = 'translateX(100%)';
        note.style.transition = 'all 0.3s ease-in';
        setTimeout(() => note.remove(), 300);
    }, 4000);
}

function showLoading(show) {
    document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
}

async function refreshData(tabId) {
    showLoading(true);
    try {
        let url = `${API_PATHS[tabId]}?pageSize=1000`;
        
        // Add Filters to URL
        if (tabId === 'medals') {
            const type = document.getElementById('filterMedalType').value;
            const year = document.getElementById('filterMedalYear').value;
            const medalId = document.getElementById('filterMedalId').value;
            const discId = document.getElementById('filterMedalDiscipline').value;
            
            if (type) url += `&type=${encodeURIComponent(type)}`;
            if (year) url += `&year=${encodeURIComponent(year)}`;
            if (medalId) url += `&medal_type_id=${encodeURIComponent(medalId)}`;
            if (discId) url += `&discipline_id=${encodeURIComponent(discId)}`;
        }

        if (tabId === 'athletes') {
            const fName = document.getElementById('filterAthleteFirstName').value;
            const lName = document.getElementById('filterAthleteLastName').value;
            if (fName) url += `&firstName=${encodeURIComponent(fName)}`;
            if (lName) url += `&lastName=${encodeURIComponent(lName)}`;
        }

        const response = await fetch(url);
        const result = await response.json();
        
        const items = result.items || result.data || [];
        
        if (tabId === 'countries') allCountries = items;
        if (tabId === 'athletes') allAthletes = items;
        if (tabId === 'games') allGames = items;
        if (tabId === 'disciplines') allDisciplines = items;

        if (tabId === 'medals') {
            items.forEach(item => {
                const name = item.medal_name || item.medalName;
                const id = item.medal_type_id || item.medalId;
                if (name && id) {
                    const engName = Object.keys(medalTranslations).find(k => k === name || medalTranslations[k] === name);
                    if (engName) medalTypesMap[engName] = id;
                    else medalTypesMap[name] = id;
                }
            });
        }

        const tableBody = document.querySelector(`#${tabId}Table tbody`);
        if (tableBody) {
            tableBody.innerHTML = '';
            items.forEach(item => {
                const row = createTableRow(tabId, item);
                tableBody.appendChild(row);
            });
        }

        updateAllDropdowns();

        if (tabId === 'medals') {
            loadDropdownsForMedals();
        }

    } catch (error) {
        console.error(`Error refreshing ${tabId}:`, error);
        showNotification(`Chyba pri načítaní ${tabId}`, 'error');
    } finally {
        showLoading(false);
    }
}

async function loadDropdownsForMedals() {
    if (allAthletes.length === 0) refreshData('athletes');
    if (allGames.length === 0) refreshData('games');
    if (allDisciplines.length === 0) refreshData('disciplines');
}

function updateAllDropdowns() {
    if (allCountries.length > 0) {
        updateDropdown('gameCountrySelect', allCountries);
        updateDropdown('athleteBirthCountrySelect', allCountries);
        updateDropdown('athleteDeathCountrySelect', allCountries);
    }
    if (allAthletes.length > 0) {
        updateDropdown('medalAthleteSelect', allAthletes, a => `${a.firstName || a.first_name} ${a.lastName || a.last_name}`);
    }
    if (allGames.length > 0) {
        updateDropdown('medalGameSelect', allGames, g => `${g.year} ${g.type} (${g.city})`);
    }
    if (allDisciplines.length > 0) {
        updateDropdown('medalDisciplineSelect', allDisciplines, d => `${d.name} (${d.category || '-'})`);
        updateDropdown('filterMedalDiscipline', allDisciplines);
    }

    const medalTypeSelect = document.querySelector('select[name="medal_type_id"]');
    const filterMedalId = document.getElementById('filterMedalId');
    
    if (Object.keys(medalTypesMap).length > 0) {
        [medalTypeSelect, filterMedalId].forEach(sel => {
            if (!sel) return;
            const current = sel.value;
            sel.innerHTML = sel === filterMedalId ? '<option value="">Všetky</option>' : '';
            ['Gold', 'Silver', 'Bronze'].forEach(type => {
                if (medalTypesMap[type]) {
                    const opt = document.createElement('option');
                    opt.value = medalTypesMap[type];
                    opt.textContent = medalTranslations[type];
                    sel.appendChild(opt);
                }
            });
            if (current) sel.value = current;
        });
    }
}

function updateDropdown(id, items, textFn = item => item.name) {
    const select = document.getElementById(id);
    if (!select) return;
    const currentValue = select.value;
    const firstOption = select.options[0] ? select.options[0].textContent : '-- Vyberte --';
    select.innerHTML = `<option value="">${firstOption}</option>`;
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
        const name = (item.first_name && item.last_name) ? `${item.first_name} ${item.last_name}` : `${item.firstName} ${item.lastName}`;
        const rawMedalName = item.medal_name || item.medalName || '-';
        const displayMedalName = medalTranslations[rawMedalName] || rawMedalName;
        tr.innerHTML = `<td>${name}</td><td>${item.year}</td><td>${item.discipline_name || item.disciplineName || '-'}</td><td>${displayMedalName}</td>`;
    } else if (tabId === 'athletes') {
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
        addMedalForm: { url: CRUD_PATHS.createMedal, tab: 'medals' },
        addAthleteForm: { url: CRUD_PATHS.createAthlete, tab: 'athletes' }
    };

    Object.keys(forms).forEach(id => {
        const formEl = document.getElementById(id);
        if (!formEl) return;
        formEl.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(formEl);
            const data = Object.fromEntries(formData.entries());
            
            if (id === 'addGameForm') {
                data.countryId = parseInt(data.country_id);
                delete data.country_id;
            }

            if (id === 'addMedalForm') {
                data.athleteId = parseInt(data.athlete_id);
                data.gameId = parseInt(data.olympic_games_id);
                data.disciplineId = parseInt(data.discipline_id);
                data.medalTypeId = parseInt(data.medal_type_id);
            }

            if (id === 'addAthleteForm') {
                if (data.birthCountryId) data.birthCountryId = parseInt(data.birthCountryId);
                if (data.deathCountryId) data.deathCountryId = parseInt(data.deathCountryId);
                ['birthDate', 'deathDate', 'birthPlace', 'deathPlace'].forEach(key => {
                    if (!data[key]) data[key] = null;
                });
            }

            try {
                const res = await fetch(forms[id].url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    showNotification('Záznam úspešne pridaný.');
                    formEl.reset();
                    refreshData(forms[id].tab);
                } else {
                    const err = await res.json();
                    showNotification('Chyba: ' + (err.error || 'Neznáma chyba'), 'error');
                }
            } catch (error) {
                showNotification('Chyba pripojenia k serveru.', 'error');
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
            showNotification('Záznam bol zmazaný.');
            refreshData(tabId);
        } else {
            showNotification('Chyba pri mazaní záznamu.', 'error');
        }
    } catch (error) {
        showNotification('Chyba pripojenia.', 'error');
    }
}

let currentEditTab = '';
let currentEditId = null;

async function openEditModal(tabId, item) {
    currentEditTab = tabId;
    currentEditId = item.id;

    document.getElementById('modalTitle').textContent = `Upraviť ${tabTitles[tabId] || tabId}`;
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
        fieldsContainer.appendChild(createField('firstName', 'Meno', item.firstName || item.first_name));
        fieldsContainer.appendChild(createField('lastName', 'Priezvisko', item.lastName || item.last_name));
        fieldsContainer.appendChild(createField('birthDate', 'Dátum narodenia', item.birthDate || item.birth_date, 'date'));
        fieldsContainer.appendChild(createField('birthPlace', 'Miesto narodenia', item.birthPlace || item.birth_place));
        fieldsContainer.appendChild(createSelectField('birthCountryId', 'Krajina narodenia', allCountries, item.birth_country_id || item.birthCountryId));
        fieldsContainer.appendChild(createField('deathDate', 'Dátum úmrtia', item.deathDate || item.death_date, 'date'));
        fieldsContainer.appendChild(createField('deathPlace', 'Miesto úmrtia', item.deathPlace || item.death_place));
        fieldsContainer.appendChild(createSelectField('deathCountryId', 'Krajina úmrtia', allCountries, item.death_country_id || item.deathCountryId));
    } else if (tabId === 'medals') {
        fieldsContainer.appendChild(createSelectField('athleteId', 'Športovec', allAthletes, item.athlete_id || item.athleteId, a => `${a.firstName || a.first_name} ${a.lastName || a.last_name}`));
        fieldsContainer.appendChild(createSelectField('gameId', 'Hry', allGames, item.olympic_games_id || item.gameId, g => `${g.year} ${g.type} (${g.city})`));
        fieldsContainer.appendChild(createSelectField('disciplineId', 'Disciplína', allDisciplines, item.discipline_id || item.disciplineId, d => `${d.name} (${d.category || '-'})`));
        
        const currentMedalId = item.medal_type_id || item.medalId;
        const medalOptions = [];
        ['Gold', 'Silver', 'Bronze'].forEach(type => {
            if (medalTypesMap[type]) {
                medalOptions.push({id: medalTypesMap[type], name: medalTranslations[type]});
            }
        });
        fieldsContainer.appendChild(createSelectField('medalTypeId', 'Typ medaily', medalOptions, currentMedalId));
    }

    document.getElementById('editModal').style.display = 'block';
}

function createField(name, label, value, type = 'text') {
    const div = document.createElement('div');
    div.className = 'form-group';
    div.innerHTML = `<label>${label}:</label><input type="${type}" name="${name}" value="${value || ''}" class="form-group input" style="width:100%">`;
    return div;
}

function createSelectField(name, label, items, currentValue, textFn = item => item.name) {
    const div = document.createElement('div');
    div.className = 'form-group';
    
    const labelEl = document.createElement('label');
    labelEl.textContent = `${label}:`;
    div.appendChild(labelEl);
    
    const select = document.createElement('select');
    select.name = name;
    select.className = 'form-group select';
    select.style.width = '100%';
    select.innerHTML = '<option value="">-- Vyberte --</option>';
    
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = textFn(item);
        if (item.id == currentValue) opt.selected = true;
        select.appendChild(opt);
    });
    
    div.appendChild(select);
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
    if (data.birthCountryId) data.birthCountryId = parseInt(data.birthCountryId) || null;
    if (data.deathCountryId) data.deathCountryId = parseInt(data.deathCountryId) || null;
    if (data.athleteId) data.athleteId = parseInt(data.athleteId);
    if (data.gameId) data.gameId = parseInt(data.gameId);
    if (data.disciplineId) data.disciplineId = parseInt(data.disciplineId);
    if (data.medalTypeId) data.medalTypeId = parseInt(data.medalTypeId);

    try {
        const res = await fetch(`${CRUD_PATHS[currentEditTab]}${currentEditId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (res.ok) {
            showNotification('Záznam bol úspešne aktualizovaný.');
            closeModal();
            refreshData(currentEditTab);
        } else {
            const err = await res.json();
            showNotification('Chyba: ' + (err.error || 'Neznáma chyba'), 'error');
        }
    } catch (error) {
        showNotification('Chyba pripojenia.', 'error');
    }
});

window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeModal();
    }
}
