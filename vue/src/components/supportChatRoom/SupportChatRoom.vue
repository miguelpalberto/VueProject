<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useSupportChatRoomStore } from '../../stores/supportChatRoom'

const authStore = useAuthStore();
const supportChatroomStore = useSupportChatRoomStore();
const message = ref('')
const isMinimized = ref(true)

const toggleMinimize = () => {
    isMinimized.value = !isMinimized.value
}

const sendMessage = () => {
    if (message.value.trim() === '') {
        return
    }
    supportChatroomStore.sendMessage(message.value)
    message.value = ''
}

</script>
<template>
    <div class="support-chatroom" :class="{ 'minimized': isMinimized }">
        <div class="card">
            <div class="card-header d-flex flex-row justify-content-between align-items-center" style="padding: 5px 5px;">
                <div class="card-header-title text-center d-flex flex-row align-items-center"
                    style="gap:2px; font-size:14px;">
                    <i class="bi bi-chat-dots-fill" v-if="!authStore.isAdmin" :class="{
                        'text-success': supportChatroomStore.isInRoom,
                        'text-warning': supportChatroomStore.isInRoom && !supportChatroomStore.hasAdminJoined,
                        'text-danger': supportChatroomStore.isInRoom && supportChatroomStore.isChatOver
                    }"></i>
                    <button v-else type="button" class="btn btn-light position-relative"
                        style="padding: 2px;pointer-events: none;">
                        <i class="bi bi-chat-dots-fill" :class="{
                            'text-success': supportChatroomStore.isInRoom,
                            'text-danger': supportChatroomStore.isInRoom && supportChatroomStore.isChatOver
                        }"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ supportChatroomStore.availableRoomsCount }}
                            <span class="visually-hidden">vCards in dire need of help</span>
                        </span>
                    </button>
                    &nbsp;
                    <b>Support Chatroom</b>
                </div>
                <div class="card-header-icon">
                    <button class="btn btn-sm btn-light d-flex justify-content-center align-items-center"
                        style="padding: 2px; height: 20px;" @click="toggleMinimize">
                        <i style="font-size:12px" class="bi"
                            :class="{ 'bi-caret-down-square': !isMinimized, 'bi-caret-up-square': isMinimized }"></i>
                    </button>
                </div>
            </div>
            <div v-if="!authStore.isAdmin" class="card-content" :class="{ 'd-none': isMinimized }">
                <div class="support-chatroom-content d-flex justify-content-center align-items-center"
                    v-if="!supportChatroomStore.isInRoom">
                    <button class="btn btn-dark" @click="supportChatroomStore.createRoom">
                        Ask for help!
                    </button>
                </div>
                <div v-else class="support-chatroom-content">
                    <div v-if="!supportChatroomStore.hasAdminJoined">
                        <div class="text-center text-primary">
                            <b>Waiting for an admin to join...</b>
                            &nbsp;
                            <div class="spinner-grow" role="status" style="width: 20px; height: 20px;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <button class="btn btn-sm btn-dark"
                                @click="() => supportChatroomStore.deleteRoom(supportChatroomStore.currentRoom)">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div v-else class="support-chatroom-content d-flex flex-column justify-content-between">
                        <div class="support-chatroom-chat">
                            <div class="card mb-2" style="height: auto; width:100%; border: 0; padding:0px;"
                                v-for="message in supportChatroomStore.messages" :key="message.timestamp">
                                <span class="badge rounded-pill"
                                    :class="{ 'text-bg-primary': supportChatroomStore.isMe(message), 'text-bg-secondary': !supportChatroomStore.isMe(message) }">{{
                                        supportChatroomStore.messageBadgeText(message) }} ({{ new
        Date(message.timestamp).toLocaleString('pt-PT') }})</span>
                                {{ message.value }}
                            </div>
                        </div>
                        <form @submit.prevent="sendMessage" v-if="!supportChatroomStore.isChatOver">
                            <div class="input-group input-group-sm">
                                <textarea @keydown.enter.exact.prevent="sendMessage" maxlength="255" rows="1" type="text"
                                    v-model="message" class="form-control" placeholder="Enter text" />
                                <button class="btn btn-success" type="button" @click="sendMessage">
                                    <i class="bi bi-send"></i>
                                </button>
                                <button class="btn btn-dark" type="button" @click="supportChatroomStore.leaveRoom">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </form>
                        <button v-else class="btn btn-sm btn-dark" @click="supportChatroomStore.leaveRoom">
                            Leave
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="card-content" :class="{ 'd-none': isMinimized }">
                <div class="support-chatroom-content d-flex flex-column justify-content-between"
                    v-if="!supportChatroomStore.isInRoom">
                    <div class="support-chatroom-chat">
                        <div class="text-center" v-if="!supportChatroomStore.areThereAvailableRooms">
                            <b>No vCard help requests</b>
                        </div>
                        <ul v-else style="list-style: none;padding: 0px;margin: 0px;">
                            <li class="mb-2" v-for="availableRoom in supportChatroomStore.availableRooms"
                                :key="availableRoom.name">
                                <div class="d-flex justify-content-between flex-row align-items-center">
                                    <b style="font-size: 12px;">
                                        <span class="text-primary">({{new Date(availableRoom.timeCreated).toLocaleString('pt-PT') }}):</span>
                                        {{ availableRoom.vcard.name }} ({{ availableRoom.vcard.username }}) is requesting help!</b>
                                    <button class="btn btn-dark btn-sm"
                                        @click="supportChatroomStore.joinRoom(availableRoom)">
                                        Join
                                    </button>
                                </div>
                                <hr>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-else class="support-chatroom-content d-flex flex-column justify-content-between">
                    <div class="support-chatroom-chat">
                        <div class="card mb-2" style="height: auto; width:100%; border: 0; padding:0px;"
                            v-for="message in supportChatroomStore.messages" :key="message.timestamp">
                            <span class="badge rounded-pill"
                                :class="{ 'text-bg-primary': supportChatroomStore.isMe(message), 'text-bg-secondary': !supportChatroomStore.isMe(message) }">{{
                                    supportChatroomStore.messageBadgeText(message) }} ({{ new
        Date(message.timestamp).toLocaleString('pt-PT') }})</span>
                            {{ message.value }}
                        </div>
                    </div>
                    <form @submit.prevent="sendMessage" v-if="!supportChatroomStore.isChatOver">
                        <div class="input-group input-group-sm">
                            <textarea @keydown.enter.exact.prevent="sendMessage" maxlength="255" rows="1" type="text"
                                v-model="message" class="form-control" placeholder="Enter text" />
                            <button class="btn btn-success" type="button" @click="sendMessage">
                                <i class="bi bi-send"></i>
                            </button>
                            <button class="btn btn-dark" type="button" @click="supportChatroomStore.leaveRoom">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </form>
                    <button v-else class="btn btn-sm btn-dark" @click="supportChatroomStore.leaveRoom">
                        Leave
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.support-chatroom {
    display: flex;
    flex-direction: column;
    height: 300px;
    width: 374px;
    box-sizing: border-box;
    background-color: #ffffff;
    border-radius: 0.5rem;
    box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
    z-index: 2000;
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    background-color: whitesmoke;
    opacity: 90%;
}

.support-chatroom.minimized {
    height: 43px;
    width: 200px;
}

.card-content {
    padding: 0.5rem;
    height: 100%;
}

textarea {
    resize: none;
}

.support-chatroom-content {
    height: 100%;
}

.support-chatroom-chat {
    width: 100%;
    max-height: 195px;
    overflow: scroll;
    overflow-x: hidden;
    padding: 0.5rem;

}

.card {
    height: 100%;
}


/* width */
::-webkit-scrollbar {
    width: 8px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #ffffff;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.bi {
    margin: 0 !important;
}
</style>