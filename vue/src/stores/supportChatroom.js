import { ref, inject, computed } from 'vue'
import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

export const useSupportChatRoomStore = defineStore('supportChatroom', () => {
    const socket = inject('socket')
    const authStore = useAuthStore()

    const currentRoom = ref(null)
    const availableRooms = ref([])
    const isInRoom = computed(() => {
        return currentRoom.value !== null
    })

    const isChatOver = computed(() => {
        if (currentRoom.value) {
            return currentRoom.value.isChatOver
        }

        return false
    })

    const hasAdminJoined = computed(() => {
        if (currentRoom.value) {
            return currentRoom.value.admin !== null
        }

        return false
    })

    const areThereAvailableRooms = computed(() => {
        return availableRooms.value.length > 0
    })

    const availableRoomsCount = computed(() => {
        return availableRooms.value.length
    })

    const messages = computed(() => {
        if (currentRoom.value) {
            //order by timestamp desc
            return currentRoom.value.messages.sort((a, b) => b.timestamp - a.timestamp)
        }
        return []
    })

    const isMe = (message) => {
        return message.sender === authStore.user.name
    }

    const messageBadgeText = (message) => {
        if (isMe(message)) {
            return 'Me'
        }
        return message.sender
    }

    const composeMessage = (message, roomName) => {
        return {
            value: message,
            sender: authStore.user.name,
            timestamp: Date.now(),
            roomName: roomName
        }
    }

    const composeRoom = (vCard) => {
        return {
            name: vCard.username + "_supportChatroom",
            vcard: vCard,
            admin: null,
            messages: [],
            isChatOver: false,
            timeCreated: Date.now()
        }
    }

    const sendMessage = (message) => {
        if (!authStore.isAuthenticated && !currentRoom.value) {
            return
        }
        const newMessage = composeMessage(message, currentRoom.value.name)
        socket.emit('supportChatroomSendMessage', newMessage)
    }

    const createRoom = () => {
        if (!authStore.isAuthenticated && authStore.isAdmin) {
            return
        }

        const newRoom = composeRoom(authStore.user)
        socket.emit('createSupportChatroom', newRoom)
        currentRoom.value = newRoom
    }

    const joinRoom = (room) => {
        if (!authStore.isAuthenticated && !currentRoom.value && !authStore.isAdmin) {
            return
        }

        availableRooms.value = availableRooms.value.filter((r) => r.name !== room.name)
        room.admin = authStore.user
        socket.emit('joinSupportChatroom', room)
        currentRoom.value = room
    }

    const leaveRoom = () => {
        if (!authStore.isAuthenticated && !currentRoom.value) {
            return
        }
        const room = { ...currentRoom.value }
        room.isChatOver = true
        currentRoom.value = null

        if (authStore.isAdmin) {
            socket.emit('leaveSupportChatroom', room)

            socket.emit('supportChatroomSendMessage', {
                value: "Our support team member has left the chat. If you have any other questions, please ask for help again. Our team will be happy to help you! :)",
                sender: "System",
                timestamp: Date.now(),
                roomName: room.name
            })
        }

        if (!authStore.isAdmin) {
            socket.emit('leaveSupportChatroom', room)
            socket.emit('supportChatroomSendMessage', {
                value: "The vCard user has left the chat.",
                sender: "System",
                timestamp: Date.now(),
                roomName: room.name
            })
            deleteRoom(room)
        }

    }

    const deleteRoom = (room) => {
        if (!authStore.isAuthenticated && !room && !authStore.isAdmin) {
            return
        }

        socket.emit('deleteSupportChatroom', room)
        currentRoom.value = null
    }

    //common
    socket.on('supportChatroomSendMessage', (message) => {
        if (!authStore.isAuthenticated || !currentRoom.value) {
            return
        }
        currentRoom.value.messages.push(message)
    })

    //admin only
    socket.on('createSupportChatroom', (room) => {
        availableRooms.value.push(room)
    })

    socket.on('supportChatroomFull', (room) => {
        availableRooms.value = availableRooms.value.filter((r) => r.name !== room.name)
    })

    socket.on('availableSupportChatRooms', (rooms) => {
        availableRooms.value = rooms
    })

    //vcard only
    socket.on('joinSupportChatroom', (room) => {
        currentRoom.value = room
    })

    socket.on('leaveSupportChatroom', (room) => {
        currentRoom.value = room
    })

    return { currentRoom, availableRooms, availableRoomsCount, isChatOver, isMe, messageBadgeText, areThereAvailableRooms, isInRoom, hasAdminJoined, messages, sendMessage, createRoom, joinRoom, leaveRoom, deleteRoom }
})
