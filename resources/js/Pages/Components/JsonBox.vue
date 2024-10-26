<template>
    <div class="json-container">
        <div v-html="formattedJson" class="json-output"></div>
    </div>
</template>

<script setup>

import { ref, onMounted, defineProps } from 'vue';

const props = defineProps({
    json: String
})

// const requestJson = ref(`
// {
//     "phone": "09769274236",
//     "password": "bebee7121"
// }
// `);

const formattedJson = ref({});

const formatJson = () => {

    let parseJson = JSON.parse(props.json);

    const jsonString = JSON.stringify(parseJson, null, 2);

    formattedJson.value = jsonString
        .replace(/({|}|\[|\])/g, '<span class="bracket">$1</span>') // Style brackets
        .replace(/"([^"]+)":/g, '<span class="key">"$1":</span>'); // Style values
}

onMounted(() => {
    formatJson();
})

</script>

<style>
/* Container styling */
.json-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 800px;
    margin-top: 10px
}

/* Textarea styling */
textarea {
    width: 100%;
    height: 200px;
    font-family: monospace;
    font-size: 16px;
    padding: 10px;
    border: 2px solid #4CAF50;
    border-radius: 8px;
    background-color: #f5f5f5;
    color: #AC603C;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s, box-shadow 0.3s;
}

textarea:focus {
    border-color: #0d9deb;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
}

/* JSON output container */
.json-output {
    background: #080808;
    color: #f6ad83;
    border-radius: 8px;
    padding: 20px;
    overflow: auto;
    box-shadow: 0 4px 12px rgba(2, 2, 2, 0.481);
    font-family: 'Fira Code', monospace;
    white-space: pre-wrap;
    font-size: 16px;
}

/* JSON key styling */
.key {
    color: #7bbdf3;
    /* Pink */
    font-weight: bold;
}

/* Bracket styling */
.bracket {
    color: white;
    /* Purple */
}

/* Error styling */
.error {
    color: #ff5555;
    /* Red */
    font-weight: bold;
    font-size: 18px;
}
</style>
