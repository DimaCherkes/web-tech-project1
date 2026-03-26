document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const athleteId = urlParams.get('id');

    if (!athleteId) {
        window.location.href = '/project1/';
        return;
    }

    const fullNameElem = document.getElementById('full-name');
    const personalInfoElem = document.getElementById('personal-info');
    const participationBody = document.getElementById('participationBody');

    loadAthleteDetails(athleteId);

    async function loadAthleteDetails(id) {
        try {
            // RESTful path: /api/athletes/{id}
            const res = await fetch(`/project1/api/athletes/${id}`);
            if (!res.ok) throw new Error('Športovec nebol nájdený');

            const result = await res.json();
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

            renderParticipations(a.participations);
            setupDeleteButton(id);
        } catch (error) {
            fullNameElem.textContent = 'Chyba';
            personalInfoElem.innerHTML = `<p class="error">${error.message}</p>`;
        }
    }

    function setupDeleteButton(id) {
        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                if (!confirm('Naozaj vymazať?')) return;

                const res = await fetch(`/project1/api/athletes/${id}`, {
                    method: 'DELETE'
                });

                if (res.status === 204) {
                    window.location.href = '/project1/';
                } else {
                    alert('Chyba pri mazaní.');
                }
            });
        }
    }

    function renderParticipations(participations) {
        participationBody.innerHTML = '';
        if (participations.length === 0) {
            participationBody.innerHTML = '<tr><td colspan="6">Neboli nájdené žiadne záznamy o účasti.</td></tr>';
            return;
        }

        participations.forEach(p => {
            const row = document.createElement('tr');
            
            // Localized medal names
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
