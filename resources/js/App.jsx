import React, {useEffect, useState} from 'react';
import axios from "axios";
import Call from "./Components/Call.jsx";
import AC from "agora-chat";
import Chat from "./Components/Chat.jsx";
import {AgoraRTCProvider} from "agora-rtc-react";
import AgoraRTC from "agora-rtc-sdk-ng";

const App = () => {
    const [orderDetails, setOrderDetails] = useState([]);
    const conn = new AC.connection({
        appKey: "611168575#1355784",
    });

    useEffect(() => {
        const id = window.reservation_id;
        const url = `/api/v1/profile/reservations/${id}/session/join`;
        axios.post(url, {}, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${window.token}`
            }
        }).then((data) => {
            setOrderDetails(data.data.data);
        })
    }, []);


    useEffect(() => {
        conn.open({
            user: orderDetails.partner?.id,
            agoraToken: orderDetails.partner?.token,
        });
    }, [orderDetails])
    return <>
        {orderDetails.type === 'voice'
            ? (<Chat
                partner={orderDetails.partner}
                client={orderDetails.client}
                conn={conn}
            />) :
            (<Call token={orderDetails.agora_token}
                   channel={orderDetails.channel}
                   partner={orderDetails.partner}
            />)
        }

    </>
};

export default App;
