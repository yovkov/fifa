<template>

    <Head title="Games" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Games
            </h2>
        </template>

        <div class="flex">
            <div class="w-full">
                <SwitchGroup as="div" class="flex items-center mx-auto w-max mt-4 cursor-pointer">
                    <Switch v-model="showFinished"
                        :class="[showFinished ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span aria-hidden="true"
                            :class="[showFinished ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                    </Switch>
                    <SwitchLabel as="span" class="ml-3">
                        <span class="text-md font-medium text-gray-900">Show finished games</span>
                    </SwitchLabel>
                </SwitchGroup>

                <div class="w-full mx-auto sm:px-6 lg:px-8 mt-4" v-for="game in allGames" :key="game.id"
                    :class="{ 'hidden': (game.finished && !showFinished) }">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 relative">
                        <div class="flex w-full items-start">
                            <div class="w-2/5 text-right flex items-center justify-end p-2">
                                <span>{{ game.home.name }}</span>
                                <img :src="game.home.flag" alt="" class="h-4 w-auto mx-2">
                            </div>
                            <div class="w-1/5 mx-2 text-center">
                                <span
                                    class="font-bold text-red-500 block mb-2 p-2">{{ game.home_score }}-{{ game.away_score }}</span>
                            </div>
                            <div class="w-2/5 text-left items-center flex justify-start p-2">
                                <img :src="game.away.flag" alt="" class="h-4 w-auto mx-2">
                                <span>{{ game.away.name }}</span>
                            </div>
                        </div>
                        <div class="w-3/5 items-center justify-center text-center mx-auto">
                            <div class="w-fit whitespace-nowrap mx-auto px-12 text-gray-400 p-2 rounded-lg border-[1px] border-gray-400 mb-2 block"
                                :class="[(game.home_score == game.prediction.home_score && game.away_score == game.prediction.away_score) ? 'bg-green-100' : 'bg-red-100']"
                                v-if="game.prediction && moment().isAfter(game.game_date)">
                                {{ game.prediction.home_score }}-{{ game.prediction.away_score }}
                            </div>
                            <div class="w-fit mx-auto" v-if="game.prediction && moment().isBefore(game.game_date)">
                                <div
                                    class="w-fit text-gray-400 p-2 rounded-lg border-[1px] border-gray-400 mb-2 block relative">
                                    <div class="w-fit whitespace-nowrap px-12">
                                        {{ predictionInputs[game.id + '_home_score'] }}-{{ predictionInputs[game.id + '_away_score'] }}
                                    </div>
                                    <PencilIcon @click="showPredictionInput(game)"
                                        class="absolute top-2 right-2 h-6 w-6 text-gray-500 rounded-full bg-gray-100 p-1" />
                                </div>
                            </div>

                            <div class="w-min mx-auto whitespace-nowrap cursor-pointer py-2 px-4 rounded-full mb-2 block bg-blue-600 text-white text-xs"
                                v-if="!game.prediction && moment().isBefore(game.game_date) && !predictionShow.includes(game.id)"
                                @click="showPredictionInput(game)">
                                Add prediction
                            </div>
                            <div class="w-fit mx-auto whitespace-nowrap">
                                <form @submit.prevent="savePrediction(game)">
                                    <div v-if="predictionShow.includes(game.id)" class="flex w-full mx-auto">
                                        <input pattern="[0-9]{2}" type="number"
                                            v-model="predictionInputs[game.id + '_home_score']" min="0" name="home_score"
                                            :id="game.id + '_home_score'"
                                            class="w-1/2 text-center relative block rounded-none rounded-l border-gray-300 bg-transparent focus:z-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <input pattern="[0-9]{2}" type="number"
                                            v-model="predictionInputs[game.id + '_away_score']" min="0" name="away_score"
                                            :id="game.id + '_away_score'"
                                            class="w-1/2 text-center relative block rounded-none rounded-r border-gray-300 bg-transparent focus:z-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div v-if="predictionShow.includes(game.id)" class="w-full mx-auto flex mt-2 mb-2">
                                        <button
                                            class="w-1/2 cursor-pointer bg-red-600 text-white px-2 py-2 text-xs rounded-full mr-2"
                                            @click="cancelPredictionInput(game)">Cancel</button>
                                        <input type="submit" value="Save"
                                            class="w-1/2 cursor-pointer bg-green-600 text-white px-2 py-2 text-xs  rounded-full">
                                    </div>
                                </form>
                            </div>

                            <div class="whitespace-nowrap text-gray-400" v-if="moment().isBefore(game.game_date)">
                                {{ formatDate(game.game_date) }}</div>

                        </div>
                        <ChevronDownIcon v-if="!selectedGame || selectedGame.id != game.id" @click="showDetails(game)"
                            class="mx-auto h-8 w-8 text-gray-500 rounded-full bg-gray-100 p-1 hover:bg-blue-600 hover:text-white cursor-pointer" />
                        <ChevronUpIcon v-if="selectedGame && selectedGame.id == game.id" @click="hideDetails(game)"
                            class="mx-auto h-8 w-8 text-gray-500 rounded-full bg-gray-100 p-1 hover:bg-blue-600 hover:text-white cursor-pointer" />

                        <div class="w-full mx-auto sm:px-6 lg:px-8 mt-4 md:flex" v-if="selectedGame.id == game.id">
                            <div v-if="selectedGame.type == 'group'"
                                class="sm:w-full md:w-1/2 overflow-hidden sm:rounded-lg p-4 bg-slate-100 sm:mb-4 md:mb-0">
                                <h1 class="text-lg font-bold">Group {{ selectedGroupStandings[0].group }}</h1>
                                <table class="table-auto w-full" cellpadding="10">
                                    <thead>
                                        <tr class="h-8 bg-gray-100">
                                            <th class="text-left">Team</th>
                                            <th class="text-right">MP</th>
                                            <th class="text-right">Pts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="team in selectedGroupStandings" :key="team.team_id" class="h-8">
                                            <td class="text-left flex">
                                                <img :src="team.team.flag" alt=""
                                                    class="h-4 w-auto max-w-[25px] mx-2" />
                                                <span>{{ team.team.name }}</span>
                                            </td>
                                            <td class="text-right">{{ team.mp }}</td>
                                            <td class="text-right">{{ team.pts }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="sm:w-full md:w-1/2 overflow-hidden sm:rounded-lg p-4 md:ml-4 bg-slate-100">
                                <h1 class="text-lg font-bold">All predictions so far</h1>
                                <table class="table-auto w-full" cellpadding="10">
                                    <thead>
                                        <tr class="h-8 bg-gray-100">
                                            <th class="text-left">User</th>
                                            <th class="text-right">
                                                <span>{{ selectedGame.home.name }}</span>
                                                <img :src="selectedGame.home.flag" alt=""
                                                    class="h-4 w-auto max-w-[25px] mx-2 inline-block">
                                            </th>
                                            <th class="text-left">
                                                <img :src="selectedGame.away.flag" alt=""
                                                    class="h-4 w-auto max-w-[25px] mx-2 inline-block">
                                                <span>{{ selectedGame.away.name }}</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="prediction in selectedGame.all_predictions" :key="prediction.id"
                                            class="h-8">
                                            <td class="text-left">
                                                <span>{{ prediction.user.name }}</span>
                                            </td>
                                            <td class="text-right">{{ prediction.home_score }}</td>
                                            <td class="text-left">{{ prediction.away_score }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import moment from 'moment';
import { ref, onMounted } from 'vue';
import { PencilIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/20/solid'
import axios from 'axios';
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        PencilIcon,
        ChevronDownIcon,
        ChevronUpIcon,
        Switch,
        SwitchGroup,
        SwitchLabel
    },

    props: {
        games: Object,
        standings: Object,
    },

    setup(props) {
        const showFinished = ref(false)
        const allGames = ref(props.games)
        const allStandings = ref(props.standings)
        const selectedGroupStandings = ref()
        const selectedGame = ref()
        const predictionInputs = ref([])
        const predictionShow = ref([])
        const formatDate = function (date) {
            return moment(date).format('DD.MM HH:mm');
        }

        const showPredictionInput = function (game) {
            predictionShow.value.push(game.id);
        }

        const hidePredictionInput = function (game) {
            predictionShow.value = predictionShow.value.filter(item => item !== game.id)
        }

        const cancelPredictionInput = function (game) {
            predictionInputs.value[game.id + '_home_score'] = game.prediction ? game.prediction.home_score : 0
            predictionInputs.value[game.id + '_away_score'] = game.prediction ? game.prediction.away_score : 0
            predictionShow.value = predictionShow.value.filter(item => item !== game.id)
        }

        const savePrediction = function (game) {
            axios.post('/predictions/save', {
                game_id: game.id,
                home_score: predictionInputs.value[game.id + '_home_score'],
                away_score: predictionInputs.value[game.id + '_away_score']
            })
                .then((response) => {
                    allGames.value = response.data.data.games
                    hidePredictionInput(game)
                })
                .catch(function (error) {
                    alert(error.response.data.message)
                });
        }

        const showDetails = function (game) {
            selectedGame.value = game
            if (game.type == 'group') {
                selectedGroupStandings.value = allStandings.value.filter(o => o.group == game.group)
            }
            else {
                selectedGroupStandings.value = null
            }
        }

        const hideDetails = function (game) {
            selectedGame.value = null
            selectedGroupStandings.value = null
        }

        onMounted(() => {
            allGames.value.forEach(game => {
                predictionInputs.value[game.id + '_home_score'] = game.prediction ? game.prediction.home_score : 0
                predictionInputs.value[game.id + '_away_score'] = game.prediction ? game.prediction.away_score : 0
            })
        });

        return {
            formatDate,
            predictionShow,
            showPredictionInput,
            hidePredictionInput,
            cancelPredictionInput,
            allGames,
            predictionInputs,
            savePrediction,
            moment,
            selectedGroupStandings,
            showDetails,
            selectedGame,
            hideDetails,
            showFinished
        }

    }

}

</script>