// Base path untuk aset dan API, di-inject oleh PHP melalui window.__SLA_BASE__
const _slaBase = (typeof window !== 'undefined' && window.__SLA_BASE__) ? window.__SLA_BASE__ : '';
const API_BASE = _slaBase + "/index.php/api/monitoring";

async function fetchJSON(url, options = {}) {
  try {
    const response = await fetch(url, options);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    return data;
  } catch (error) {
    console.error("API Fetch Error:", error);
    return null;
  }
}

window.SLA_API = {
  login: (username, password) =>
    fetchJSON(`${_slaBase}/index.php/api/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ username, password }),
    }),

  getGroups: () => fetchJSON(`${_slaBase}/index.php/api/groups`),

  getWaiting: (groupId = '') => fetchJSON(`${API_BASE}/waiting` + (groupId ? `?group_id=${encodeURIComponent(groupId)}` : '')),
  getOverdue: (groupId = '') => fetchJSON(`${API_BASE}/overdue` + (groupId ? `?group_id=${encodeURIComponent(groupId)}` : '')),
  getOverdueResolved: (groupId = '') => fetchJSON(`${API_BASE}/overdue-resolved` + (groupId ? `?group_id=${encodeURIComponent(groupId)}` : '')),
  getCompleted: (groupId = '') => fetchJSON(`${API_BASE}/completed` + (groupId ? `?group_id=${encodeURIComponent(groupId)}` : '')),

  resolve: (id) =>
    fetchJSON(`${API_BASE}/${id}/resolve`, {
      method: "POST",
    }),

  escalate: (id, clientName, complaint) =>
    fetchJSON(`${API_BASE}/${id}/escalate`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        client_name: clientName,
        complaint: complaint,
      }),
    }),
};
//api.js