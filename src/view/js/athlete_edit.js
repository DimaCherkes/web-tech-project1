document.addEventListener('DOMContentLoaded', async () => {
    console.log("JS loaded, starting initialization...");
    
    const urlParams = new URLSearchParams(window.location.search);
    const athleteId = urlParams.get('id');

    if (!athleteId) {
        console.error("No athlete ID found in URL");
        window.location.href = '/project1/';
        return;
    }

    const editForm = document.getElementById('editAthleteForm');
    const birthCountrySelect = document.getElementById('birthCountryId');
    const deathCountrySelect = document.getElementById('deathCountryId');

    let allCountries = [];

    // Function to load countries
    async function loadCountries() {
        try {
            console.log("Fetching all countries from /project1/api/allCountries...");
            // Use absolute path for reliability
            const res = await fetch('/project1/api/allCountries?pageSize=1000');
            
            if (!res.ok) {
                const errorText = await res.text();
                throw new Error(`HTTP error! status: ${res.status}, body: ${errorText}`);
            }
            
            const result = await res.json();
            allCountries = result.items || [];
            console.log("Countries received:", allCountries.length);

            // Populate selects
            [birthCountrySelect, deathCountrySelect].forEach(select => {
                if (!select) return;
                select.innerHTML = '<option value="">-- Vyberte krajinu --</option>';
                allCountries.forEach(country => {
                    const opt = document.createElement('option');
                    opt.value = country.id;
                    opt.textContent = country.name;
                    select.appendChild(opt);
                });
            });
        } catch (error) {
            console.error('Failed to load countries:', error);
        }
    }

    // Function to load athlete data
    async function fetchAthleteData(id) {
        try {
            console.log(`Fetching athlete data for ID ${id}...`);
            const res = await fetch(`/project1/api/athletes/${id}`);
            if (!res.ok) throw new Error('Chyba pri načítaní dát športovca');
            
            const result = await res.json();
            const a = result.data;
            console.log("Athlete data received:", a);

            document.getElementById('athleteId').value = a.id;
            document.getElementById('firstName').value = a.firstName;
            document.getElementById('lastName').value = a.lastName;
            document.getElementById('birthDate').value = a.birthDate || '';
            document.getElementById('birthPlace').value = a.birthPlace || '';
            document.getElementById('deathDate').value = a.deathDate || '';
            document.getElementById('deathPlace').value = a.deathPlace || '';

            // Match country IDs by name
            if (a.birthCountryName) {
                const found = allCountries.find(c => c.name === a.birthCountryName);
                if (found) {
                    birthCountrySelect.value = found.id;
                    console.log("Birth country set to ID:", found.id);
                }
            }

            if (a.deathCountryName) {
                const found = allCountries.find(c => c.name === a.deathCountryName);
                if (found) {
                    deathCountrySelect.value = found.id;
                    console.log("Death country set to ID:", found.id);
                }
            }
            
        } catch (error) {
            console.error('Fetch athlete error:', error);
            alert(error.message);
        }
    }

    // Run initialization
    try {
        await loadCountries();
        await fetchAthleteData(athleteId);
    } catch (e) {
        console.error("Initialization failed", e);
    }

    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const data = {
            firstName: document.getElementById('firstName').value,
            lastName: document.getElementById('lastName').value,
            birthDate: document.getElementById('birthDate').value || null,
            birthPlace: document.getElementById('birthPlace').value || null,
            birthCountryId: parseInt(birthCountrySelect.value) || null,
            deathDate: document.getElementById('deathDate').value || null,
            deathPlace: document.getElementById('deathPlace').value || null,
            deathCountryId: parseInt(deathCountrySelect.value) || null,
        };

        console.log("Sending update data:", data);

        try {
            const res = await fetch(`/project1/api/athletes/${athleteId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (res.ok) {
                alert('Údaje boli úspešne aktualizované.');
                window.location.href = `/project1/athlete?id=${athleteId}`;
            } else {
                const err = await res.json();
                alert('Chyba: ' + (err.error || 'Neznáma chyba'));
            }
        } catch (error) {
            console.error('Update error:', error);
            alert('Chyba pri odosielaní požiadavky.');
        }
    });
});
