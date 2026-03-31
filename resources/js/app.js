const API = "http://127.0.0.1:8000/api";

let editingId = null;

document.addEventListener("DOMContentLoaded", () => {
    loadCampaigns();
});

async function loadCampaigns() {
    const res = await fetch(`${API}/campaigns`);
    const data = await res.json();

    const list = document.getElementById('campaignList');
    list.innerHTML = '';

    data.forEach(c => {
        const div = document.createElement('div');
        div.className = 'campaign';

        div.innerHTML = `
            <h3>${c.title}</h3>
            <p>${c.description ?? ''}</p>
            <p>Goal: ${c.goal_amount}</p>
            <p>Raised: ${c.current_amount}</p>
            <p>Status: ${c.is_active ? 'Open' : 'Closed'}</p>

            <button onclick="startEdit(${c.id})">Edit</button>
            <button onclick="deleteCampaign(${c.id})">Delete</button>
            <button onclick="donate(${c.id})">Donate</button>
        `;

        list.appendChild(div);
    });
}

async function createCampaign() {
    await fetch(`${API}/campaigns`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            title: document.getElementById('title').value,
            description: document.getElementById('description').value,
            goal_amount: document.getElementById('goal_amount').value
        })
    });

    loadCampaigns();
}

async function startEdit(id) {
    editingId = id;

    const res = await fetch(`${API}/campaigns/${id}`);
    const c = await res.json();

    document.getElementById('edit_title').value = c.title;
    document.getElementById('edit_description').value = c.description;
    document.getElementById('edit_goal_amount').value = c.goal_amount;

    document.getElementById('createForm').classList.add('hidden');
    document.getElementById('editForm').classList.remove('hidden');
}

function stopEditing() {
    editingId = null;

    document.getElementById('createForm').classList.remove('hidden');
    document.getElementById('editForm').classList.add('hidden');
}

async function updateCampaign() {
    await fetch(`${API}/campaigns/${editingId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            title: document.getElementById('edit_title').value,
            description: document.getElementById('edit_description').value,
            goal_amount: document.getElementById('edit_goal_amount').value
        })
    });

    stopEditing();
    loadCampaigns();
}

async function deleteCampaign(id) {
    await fetch(`${API}/campaigns/${id}`, {
        method: "DELETE"
    });

    loadCampaigns();
}

async function donate(id) {
    const amount = prompt("Donation amount:");

    if (!amount) return;

    await fetch(`${API}/campaigns/${id}/donate`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amount })
    });

    loadCampaigns();
}