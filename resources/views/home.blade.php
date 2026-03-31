<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaigns</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        input, textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
        }

        button {
            margin-right: 5px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .hidden {
            display: none;
        }

        .campaign {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .error {
            background: #ffe5e5;
            color: #b00020;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ffb3b3;
        }

        @media (max-width: 600px) {
            button {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Campaigns</h1>

    <!-- ERROR -->
    <div id="errorBox" class="error hidden"></div>

    <!-- CREATE -->
    <div id="createForm" class="card">
        <h2>Create Campaign</h2>
        <input id="title" type="text" placeholder="Title">
        <textarea id="description" placeholder="Description"></textarea>
        <input id="goal_amount" type="number" placeholder="Goal amount">
        <button onclick="createCampaign()">Create</button>
    </div>

    <!-- EDIT -->
    <div id="editForm" class="card hidden">
        <h2>Edit Campaign</h2>
        <input id="edit_title" type="text" placeholder="Title">
        <textarea id="edit_description" placeholder="Description"></textarea>
        <input id="edit_goal_amount" type="number" placeholder="Goal amount">

        <button onclick="updateCampaign()">Save</button>
        <button onclick="stopEditing()">Cancel</button>
    </div>

    <!-- LIST -->
    <div id="campaignList"></div>
</div>

<script>
const API = "http://127.0.0.1:8000/api";
console.log("script loaded");
let editingId = null;

document.addEventListener("DOMContentLoaded", () => {
    loadCampaigns();
});

function showError(message) {
    const box = document.getElementById('errorBox');
    box.textContent = message;
    box.classList.remove('hidden');
}

function clearError() {
    const box = document.getElementById('errorBox');
    box.textContent = '';
    box.classList.add('hidden');
}

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
            <p>Goal: ${Number(c.goal_amount).toFixed(2)}</p>
            <p>Raised: ${Number(c.current_amount).toFixed(2)}</p>
            <p>Status: ${c.is_active ? 'Open' : 'Closed'}</p>

            <button onclick="startEdit(${c.id})">Edit</button>
            <button onclick="deleteCampaign(${c.id})">Delete</button>
            <button onclick="donate(${c.id})">Donate</button>
        `;

        list.appendChild(div);
    });
}

async function createCampaign() {
    clearError();

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
    clearError();

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
    clearError();

    await fetch(`${API}/campaigns/${id}`, {
        method: "DELETE"
    });

    loadCampaigns();
}

async function donate(id) {
    clearError();

    console.log("Donating to campaign ID:", id);

    const input = prompt("Donation amount:");
    if (input === null) return;

    console.log("Raw input:", input);

    const amount = Number(input);
    console.log("Parsed amount:", amount);

    if (isNaN(amount)) {
        showError("Amount must be a number.");
        return;
    }

    if (amount <= 0) {
        showError("Amount must be at least 0.01.");
        return;
    }

    const decimals = (input.split('.')[1] || "").length;
    if (decimals > 2) {
        showError("Amount can have maximum 2 decimal places.");
        return;
    }

    console.log(`Sending donation: campaign_id=${id}, amount=${amount}`);

    try {
        const res = await fetch(`${API}/campaigns/${id}/donate`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ amount })
        });

        const data = await res.json();

        console.log("Response for campaign", id, ":", data);

        if (!res.ok) {
            const message =
                data.errors?.amount?.[0] ||
                data.errors?.goal_amount?.[0] ||
                data.message ||
                data.error ||
                "Validation error";

            console.error("Donation failed for campaign", id, ":", message);
            showError(message);
            return;
        }

        console.log(`Donation SUCCESS → campaign ${id}, amount ${amount}`);

        loadCampaigns();

    } catch (err) {
        console.error("Network error for campaign", id, err);
        showError("Network error");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadCampaigns();

    setInterval(() => {
        console.log("Refreshing campaigns...");
        loadCampaigns();
    }, 5000); // elke 5 seconden
});
</script>

</body>
</html>