<template>
    <div v-if="api" class="api-details mx-4">
        <h5>
            Description
        </h5>
        <p class="pt-2">{{ api.description }}</p>
        <div class="d-flex justify-content-between align-items-center mb-4 mt-5" style="width:800px">
            <h5>Endpoints</h5>
            <!-- <font-awesome-icon icon="fa-regular fa-circle-question" class="fs-5" /> -->
        </div>
        <span :class="api.method">{{ api.method }}</span>
        <span class="ms-1">{{ api.endpoints }}</span>
        <!-- <div class="mt-3">
            <div class="note py-1"><font-awesome-icon icon="fa-solid fa-circle-check" /> /id for specific user</div>
            <div class="note py-1"><font-awesome-icon icon="fa-solid fa-circle-check" /> ?name=$name for search with
                name</div>
            <div class="note py-1"><font-awesome-icon icon="fa-solid fa-circle-check" /> ?phone=$phone for search with
                phone</div>
        </div> -->
        <div class="d-flex justify-content-between align-items-center mt-5 mb-4" style="width:800px">
            <h5>Header</h5>
        </div>
        <table class="table-bordered" style="width:800px">
            <thead>
                <tr>
                    <th class="p-2" scope="col">Key</th>
                    <th class="p-2" scope="col">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-2">Content-type</td>
                    <td class="p-2">application/json</td>
                </tr>
            </tbody>
        </table>
        <div v-if="api.is_authorized" class="note py-1"><font-awesome-icon icon="fa-solid fa-circle-check" />
            Authorization: Bearer $token
        </div>
        <h5 class="mt-5">
            <div class="d-flex justify-content-between align-items-center mt-5" style="width:800px">
                <h5>Request</h5>
                <!-- <font-awesome-icon icon="fa-regular fa-circle-question" class="fs-5" /> -->
            </div>
            <div class="json-container">
                <div v-html="formatJson(api.request)" class="json-output"></div>
            </div>
        </h5>
        <h5 class="mt-5">
            <div class="d-flex justify-content-between align-items-center mt-5" style="width:800px">
                <h5>Response</h5>
                <!-- <font-awesome-icon icon="fa-regular fa-circle-question" class="fs-5" /> -->
            </div>
            <div>
                <div class="json-container">
                    <div v-html="formatJson(api.response)" class="json-output"></div>
                </div>
            </div>
        </h5>
        <h5 class="mt-5">Error responses</h5>
        <p v-if="api.error_responses.length != 0" class="mt-3">This API uses the following error codes:</p>
        <div v-else class="d-flex justify-content-center align-items-center" style="height:200px">
            No error responses
        </div>
        <ul class="pb-5">
            <li class="my-3" v-for="error in api.error_responses" :key="error">
                <span class="text-danger"
                    style="background-color:rgba(133, 0, 0, 0.1);border-radius:8px;padding:4px">{{ error.status_code }} :</span>
                {{ error.message }}
            </li>
        </ul>
    </div>
    <div v-else class="d-flex justify-content-center align-items-center">
        <img src="../../../../public/images/404.svg" class="w-100" style="margin-top:-90px">
    </div>
    <div v-if="!api && selectedId" style="position:absolute;top:70px;right:70px">
        <Link :href="'/docs/' + selectedId">
        <button class="btn text-white" style="background-color:#A78BFA">Create <font-awesome-icon
                icon="fa-solid fa-plus" /></button>
        </Link>
    </div>
    <div v-if="api" style="position:absolute;top:70px;right:70px">
        <Link :href="'/docs/edit/' + api.id">
        <button class="btn text-white" style="background-color:#A78BFA">Edit <font-awesome-icon
                icon="fa-solid fa-pen-to-square" /></button>
        </Link>
    </div>
</template>

<script setup>
import JsonBoxVue from "./JsonBox.vue";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    selectedId: Number,
    api: Object
})

const formatJson = (json) => {

    let parseJson = JSON.parse(json);

    const jsonString = JSON.stringify(parseJson, null, 2);

    return jsonString
        .replace(/({|}|\[|\])/g, '<span class="bracket">$1</span>') // Style brackets
        .replace(/"([^"]+)":/g, '<span class="key">"$1":</span>'); // Style values
}

</script>

<style scoped>
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

.api-details {
    color: white
}

.GET {
    background-color: green;
    padding: 5px 10px;
    border-radius: 10px;
}

.POST {
    background-color: rgb(248, 189, 10);
    padding: 5px 10px;
    border-radius: 10px;
}

.PUT {
    background-color: rgb(4, 114, 239);
    padding: 5px 10px;
    border-radius: 10px;
}

.DELETE {
    background-color: rgb(234, 8, 8);
    padding: 5px 10px;
    border-radius: 10px;
}

.note {
    color: #C38AEA;
    font-size: 13px;
}
</style>
