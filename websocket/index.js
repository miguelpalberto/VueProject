const httpServer = require('http').createServer()
const io = require("socket.io")(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true
    }
})
httpServer.listen(8080, () => {
    console.log('listening on *:8080')
})

io.on('connection', (socket) => {
    console.log(`client ${socket.id} has connected`);

    socket.on('loggedIn', (user) => {
        console.log(`user #${user.id} has logged in`)
        socket.join(user.id)
        if (user.type == "A") {
            socket.join("administrators")
        }
    })

    socket.on('vCardBlocked', (vCard) => {
        console.log(`vcard #${vCard.phone_number} has been blocked`)
        io.to(vCard.phone_number).emit('blocked')
    })

    socket.on('userDeleted', (user) => {
        console.log(`user #${user.id} has been deleted`)
        io.to(user.id).emit('deleted')
    })

    socket.on('vcardMaxDebitChanged', (vCard) => {
        console.log(`vcard #${vCard.phone_number} max debit has been changed`)
        io.to(vCard.phone_number).emit('deleted')
    })

    socket.on('newCreditTransaction', (transaction) => {
        //transação normal de vcard para vcard
        if (transaction.type == "D" && transaction.payment_type == "VCARD") {
            io.to(transaction.pair_vcard).emit('newCreditTransaction', transaction)
        }
        //transação de crédito feita por um administrador
        else{
            io.to(transaction.vcard).emit('newCreditTransaction', transaction)
        }
    })
})