const ACCESS_TOKEN = '';
const POST_ID = '218978613438837';
const TIME = 2;

const getComment = async () => {
    try {
        const { data } = await axios.get(
            `getData.php?type=getCmt&idPost=${POST_ID}&accessToken=${ACCESS_TOKEN}`
        );
        const res = data.data.map(item => {
            const { from, message } = item;
            return {
                ...from,
                message,
            };
        });
        // console.log(res);
        return Promise.resolve(res.reverse());
    } catch (error) {
        console.error(error);
        return Promise.reject(error);
    }
};

const getReact = async type => {
    try {
        const { data } = await axios.get(
            `getData.php?type=getReact&idPost=${POST_ID}&accessToken=${ACCESS_TOKEN}&typeReact=${type}`
        );
        return Promise.resolve(data.data);
    } catch (error) {
        console.error(error);
        return Promise.reject(error);
    }
};

const divComment = ({ id, name, message }) => {
    return `<div class="user-cmt">
    <div class="user-avt" style="background-image: url(https://graph.facebook.com/${id}/picture?height=30&width=30&access_token=6628568379%7Cc1e620fa708a1d5696fb991c1bde5662);"></div>
    <div class="user-info">
        <h3 class="user-name">${name}</h3>
        <p class="user-content">${message}</p>
    </div>
</div>`;
};

const renderReact = mapReact => {
    document.getElementById('count-like').innerText = mapReact.get('LIKE');
    document.getElementById('count-love').innerText = mapReact.get('LOVE');
    document.getElementById('count-haha').innerText = mapReact.get('HAHA');
    document.getElementById('count-wow').innerText = mapReact.get('WOW');
    document.getElementById('count-sad').innerText = mapReact.get('SAD');
    document.getElementById('count-angry').innerText = mapReact.get('ANGRY');
};

setInterval(async () => {
    try {
        const listDataRender = await Promise.all([getComment(), getReact()]);
        const [comments, listReact] = listDataRender;
        const render = comments.map(divComment);
        document.getElementById('comment').innerHTML = render.join('');

        const mapReact = new Map();
        mapReact.set('LIKE', 0);
        mapReact.set('LOVE', 0);
        mapReact.set('HAHA', 0);
        mapReact.set('WOW', 0);
        mapReact.set('SAD', 0);
        mapReact.set('ANGRY', 0);

        listReact.forEach(({ type }) => {
            const curr = mapReact.get(type);
            mapReact.set(type, curr + 1);
        });
        renderReact(mapReact);
        // console.log(mapReact);
    } catch (error) {
        console.error(error);
    }
}, 2 * 1000);
