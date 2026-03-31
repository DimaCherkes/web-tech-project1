console.log("athlete.js v2 loaded");

document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed");
    
    const urlParams = new URLSearchParams(window.location.search);
    const athleteId = urlParams.get('id');

    console.log("Found Athlete ID in URL:", athleteId);

    if (!athleteId) {
        console.error("No athlete ID found in URL. Redirecting...");
        // window.location.href = '/project1/';
        return;
    }

    const fullNameElem = document.getElementById('full-name');
    const personalInfoElem = document.getElementById('personal-info');
    const participationBody = document.getElementById('participationBody');

    // Setup buttons
    setupAdminButtons(athleteId);

    // Fetch details
    loadAthleteDetails(athleteId);

    async function loadAthleteDetails(id) {
        console.log("Fetching details for athlete ID:", id);
        try {
            const res = await fetch(`/project1/api/athletes/${id}`);
            console.log("Fetch response status:", res.status);
            
            if (!res.ok) throw new Error('Športovec nebol nájdený');

            const result = await res.json();
            console.log("Fetched Data:", result);
            const a = result.data;

            fullNameElem.textContent = `${a.firstName} ${a.lastName}`;
            
            personalInfoElem.innerHTML = `
                <p><strong>Dátum narodenia:</strong> ${a.birthDate || '-'}</p>
                <p><strong>Miesto narodenia:</strong> ${a.birthPlace || '-'} (${a.birthCountryName || '-'})</p>
                ${a.deathDate ? `
                    <p><strong>Dátum úmrtia:</strong> ${a.deathDate}</p>
                    <p><strong>Miesto úmrtia:</strong> ${a.deathPlace || '-'} (${a.deathCountryName || '-'})</p>
                ` : '<p><strong>Stav:</strong> Žije / Informácia nie je dostupná</p>'}
            `;

            renderParticipations(a.participations || []);
        } catch (error) {
            console.error('Error fetching athlete details:', error);
            fullNameElem.textContent = 'Chyba';
            if (personalInfoElem) personalInfoElem.innerHTML = `<p class="error">${error.message}</p>`;
        }
    }

    function setupAdminButtons(id) {
        console.log("Setting up admin buttons for ID:", id);
        const updateBtn = document.getElementById('updateBtn');
        const deleteBtn = document.getElementById('deleteBtn');

        if (updateBtn) {
            console.log("Update button found, adding listener");
            updateBtn.onclick = function(e) {
                console.log("Update click triggered");
                e.preventDefault();
                window.location.href = `/project1/athlete/edit?id=${id}`;
            };
        } else {
            console.log("Update button NOT found in DOM");
        }

        if (deleteBtn) {
            console.log("Delete button found, adding listener");
            deleteBtn.onclick = async function(e) {
                console.log("Delete click triggered");
                e.preventDefault();
                if (!confirm('Naozaj vymazať?')) return;

                try {
                    const res = await fetch(`/project1/api/athletes/${id}`, {
                        method: 'DELETE'
                    });
                    console.log("Delete status:", res.status);

                    if (res.status === 204) {
                        alert('Športovec bol úspešne vymazaný.');
                        window.location.href = '/project1/';
                    } else {
                        const err = await res.json();
                        alert('Chyba pri mazaní: ' + (err.error || 'Neznáma chyba'));
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    alert('Chyba pri komunikácii so serverom.');
                }
            };
        } else {
            console.log("Delete button NOT found in DOM");
        }
    }

    function renderParticipations(participations) {
        if (!participationBody) return;
        participationBody.innerHTML = '';
        if (!participations || participations.length === 0) {
            participationBody.innerHTML = '<tr><td colspan="6">Neboli nájdené žiadne záznamy o účasti.</td></tr>';
            return;
        }

        participations.forEach(p => {
            const row = document.createElement('tr');
            let placement = p.medalName || p.placing;
            if (p.medalName === 'Gold') placement = 'Zlato';
            if (p.medalName === 'Silver') placement = 'Striebro';
            if (p.medalName === 'Bronze') placement = 'Bronz';

            row.innerHTML = `
                <td>${p.year}</td>
                <td>${p.type}</td>
                <td>${p.city}</td>
                <td>${p.disciplineName}</td>
                <td>${p.category || '-'}</td>
                <td class="medal-${(p.medalName || '').toLowerCase()}">
                    ${placement}
                </td>
            `;
            participationBody.appendChild(row);
        });
    }
});
