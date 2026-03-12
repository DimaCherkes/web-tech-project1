document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const athleteId = urlParams.get('id');

    if (!athleteId) {
        window.location.href = '/';
        return;
    }

    const fullNameElem = document.getElementById('full-name');
    const personalInfoElem = document.getElementById('personal-info');
    const participationBody = document.getElementById('participationBody');

    loadAthleteDetails(athleteId);

    async function loadAthleteDetails(id) {
        try {
            const res = await fetch(`/project1/api/athlete?id=${id}`);
            if (!res.ok) throw new Error('Athlete not found');
            
            const result = await res.json();
            const a = result.data;

            fullNameElem.textContent = `${a.firstName} ${a.lastName}`;
            
            personalInfoElem.innerHTML = `
                <p><strong>Birth Date:</strong> ${a.birthDate || '-'}</p>
                <p><strong>Birth Place:</strong> ${a.birthPlace || '-'} (${a.birthCountryName || '-'})</p>
                ${a.deathDate ? `
                    <p><strong>Death Date:</strong> ${a.deathDate}</p>
                    <p><strong>Death Place:</strong> ${a.deathPlace || '-'} (${a.deathCountryName || '-'})</p>
                ` : '<p><strong>Status:</strong> Alive / Information not available</p>'}
            `;

            renderParticipations(a.participations);
        } catch (error) {
            fullNameElem.textContent = 'Error';
            personalInfoElem.innerHTML = `<p class="error">${error.message}</p>`;
        }
    }

    function renderParticipations(participations) {
        participationBody.innerHTML = '';
        if (participations.length === 0) {
            participationBody.innerHTML = '<tr><td colspan="6">No participation records found.</td></tr>';
            return;
        }

        participations.forEach(p => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${p.year}</td>
                <td>${p.type}</td>
                <td>${p.city}</td>
                <td>${p.disciplineName}</td>
                <td>${p.category || '-'}</td>
                <td class="medal-${(p.medalName || '').toLowerCase()}">
                    ${p.medalName || p.placing}
                </td>
            `;
            participationBody.appendChild(row);
        });
    }
});
