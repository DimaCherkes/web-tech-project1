document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    const pageSize = 10;
    let sortBy = 'id';
    let sortDir = 'ASC';
    let currentCategory = '';
    let currentYear = '';

    const categoryFilter = document.getElementById('categoryFilter');
    const yearFilter = document.getElementById('yearFilter');
    const athletesBody = document.getElementById('athletesBody');
    const pageInfo = document.getElementById('pageInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const tableHeaders = document.querySelectorAll('#athletesTable th');

    // Initial load
    loadFilters();
    loadAthletes();

    // Event Listeners
    categoryFilter.addEventListener('change', (e) => {
        currentCategory = e.target.value;
        currentPage = 1;
        loadAthletes();
    });

    yearFilter.addEventListener('change', (e) => {
        currentYear = e.target.value;
        currentPage = 1;
        loadAthletes();
    });

    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            loadAthletes();
        }
    });

    nextPageBtn.addEventListener('click', () => {
        currentPage++;
        loadAthletes();
    });

    tableHeaders.forEach(th => {
        th.addEventListener('click', () => {
            const field = th.dataset.sort;
            if (sortBy === field) {
                sortDir = sortDir === 'ASC' ? 'DESC' : 'ASC';
            } else {
                sortBy = field;
                sortDir = 'ASC';
            }
            
            updateSortClasses(th);
            loadAthletes();
        });
    });

    async function loadFilters() {
        try {
            const [categoriesRes, yearsRes] = await Promise.all([
                fetch('/project1/api/categories'),
                fetch('/project1/api/years')
            ]);
            
            const categoriesData = await categoriesRes.json();
            const yearsData = await yearsRes.json();

            categoriesData.data.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                categoryFilter.appendChild(opt);
            });

            yearsData.data.forEach(year => {
                const opt = document.createElement('option');
                opt.value = year;
                opt.textContent = year;
                yearFilter.appendChild(opt);
            });
        } catch (error) {
            console.error('Error loading filters:', error);
        }
    }

    async function loadAthletes() {
        try {
            const params = new URLSearchParams({
                page: currentPage,
                pageSize: pageSize,
                sortBy: sortBy,
                sortDir: sortDir,
                category: currentCategory,
                year: currentYear
            });

            // Use RESTful endpoint
            const res = await fetch(`/project1/api/athletes?${params.toString()}`);
            const result = await res.json();

            renderTable(result.data);
            updatePagination(result.pagination);
        } catch (error) {
            console.error('Error loading athletes:', error);
        }
    }

    function renderTable(athletes) {
        athletesBody.innerHTML = '';
        athletes.forEach(a => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            row.onclick = () => window.location.href = `/project1/athlete?id=${a.id}`;
            row.innerHTML = `
                <td>${a.id}</td>
                <td>${a.firstName}</td>
                <td>${a.lastName}</td>
                <td>${a.year}</td>
                <td>${a.country || '-'}</td>
                <td>${a.sportName || '-'}</td>
            `;
            athletesBody.appendChild(row);
        });
    }

    function updatePagination(pagination) {
        pageInfo.textContent = `Page ${pagination.page} of ${pagination.totalPages}`;
        prevPageBtn.disabled = pagination.page <= 1;
        nextPageBtn.disabled = pagination.page >= pagination.totalPages;
        currentPage = pagination.page;
    }

    function updateSortClasses(activeTh) {
        tableHeaders.forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        activeTh.classList.add(sortDir === 'ASC' ? 'sort-asc' : 'sort-desc');
    }
});
