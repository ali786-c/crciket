package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.devwithguru.cricket.data.db.entity.WicketEventEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface WicketEventDao {
    @Query("SELECT * FROM wicket_events WHERE inningsId = :inningsId ORDER BY wicketNumber")
    fun getWicketsByInnings(inningsId: Long): Flow<List<WicketEventEntity>>

    @Query("SELECT * FROM wicket_events WHERE inningsId = :inningsId ORDER BY wicketNumber")
    suspend fun getWicketsByInningsSync(inningsId: Long): List<WicketEventEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertWicket(wicket: WicketEventEntity): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(wickets: List<WicketEventEntity>)

    @Query("DELETE FROM wicket_events WHERE inningsId = :inningsId")
    suspend fun deleteAllByInnings(inningsId: Long)
}
