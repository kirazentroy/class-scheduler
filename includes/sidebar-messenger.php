<style>
    .side {
        background: #000814;
    }

    .conversation-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .conversation-list li {
        padding: 10px 10px 0;
        border-bottom: 1px solid #ccc;
        cursor: pointer;
        color: white;
    }

    .conversation-list li:hover {
        background-color: #f0f0f0;
    }

    #sidebar-wrapper::-webkit-scrollbar {
        width: 10px;
        /* height: 10px; */
        max-height: 20%;
        height: 20%;
    }

    #sidebar-wrapper::-webkit-scrollbar-track {
        margin: 20px 0;
    }


    #sidebar-wrapper::-webkit-scrollbar-thumb {
        background: #888;
    }

    /* Handle on hover */
    #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .list-container {
        padding-top: 50px;
    }

    .conversationwith {
        position: absolute;
        display: flex;
        justify-content: center;
        text-align: center;
        align-items: center;
        top: 15px;
        right: 4px;
        height: 15px;
        width: 15px;
        font-size: 10px;
        border-radius: 50%;
        background-color: red;
        color: white;
    }
</style>

<div class="side position-fixed" id="sidebar-wrapper" style="overflow-y: scroll;">
    <div class="position-fixed" style="z-index: 3; background: #000814; width: 15%;">
        <div class="pichead text-center w-100 px-4 py-3">
            <h4 class="fw-bold text-white mt-1">Chats</h4>
        </div>
        <div class="divider mb-3"></div>
    </div>
    <div class="list-container list-group position-relative mt-5">
        <ul id="conversation-list" class="conversation-list">

        </ul>
    </div>
</div>
<script src="../assets/jquery/jquery.js"></script>