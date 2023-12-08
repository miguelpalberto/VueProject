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

    socket.on('userBlocked', (user) => {
        console.log(`user #${user.id} has been blocked`)
        io.to(user.id).emit('blocked')
    })

    socket.on('userDeleted', (user) => {
        console.log(`user #${user.id} has been deleted`)
        io.to(user.id).emit('deleted')
    })

    socket.on('newCreditTransaction', (transaction) => {
        if (transaction.payment_type == "VCARD") {
            io.to(transaction.pair_vcard).emit('newCreditTransaction', transaction)
        }
        else{
            io.to(transaction.vcard).emit('newCreditTransaction', transaction)
        }
    })
})